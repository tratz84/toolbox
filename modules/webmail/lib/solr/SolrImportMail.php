<?php


namespace webmail\solr;

use core\exception\SolrException;
use core\parser\HtmlParser;


class SolrImportMail {
    
    protected $contextName;
    protected $solrUrl;
    
    protected $documents = array();
    protected $documentCount = 1;
    
    public function __construct($solrUrl) {
        $this->contextName = \core\Context::getInstance()->getContextName();
        
        $this->setSolrUrl( $solrUrl );
        
    }
    
    public function setSolrUrl($url) { $this->solrUrl = $url; }
    public function getSolrUrl() { return $this->solrUrl; }
    
    
    public function getProperties($emlFile) {
        $r = array();
        
        $pf = $emlFile . '.properties';
        
        if (file_exists($pf)) {
            $r = json_decode( file_get_contents($pf), true );
        }
        
        if (is_array($r)) {
            return $r;
        } else {
            return array();
        }
    }
    
    
    public function parseEml($emlFile) {
        $props = $this->getProperties($emlFile);
        
        $p = new \PhpMimeMailParser\Parser();
        $p->setPath($emlFile);
        
        
        $datadir = \core\Context::getInstance()->getDataDir();
        
        $r = array();
        $r['id'] = str_replace($datadir, '', $emlFile);
        $r['contextName'] = $this->contextName;
        $r['file'] = str_replace($datadir, '', $emlFile);
        
        $dt = new \DateTime(null, new \DateTimeZone('+0000'));
        $dt->setTimestamp(strtotime($p->getHeader('date')));
        $r['date'] = $dt->format('Y-m-d').'T'.$dt->format('H:i:s').'Z';
        $r['subject'] = $p->getHeader('subject');
        
        
        $r['content'] = $p->getMessageBody('html');
        
        
        $hp = new HtmlParser();
        $hp->loadString( $r['content'] );
        $hp->parse();
        
        $r['content'] = $hp->getBodyText();
        
//         $r['text'] = array();
        
        if (trim($r['content']) == '') {
            $r['content'] = $p->getMessageBody('text');
//             $r['text'][] = $r['content'];
        }
        
        // fields
        $r['mailboxName'] = $props['folder'];
        $r['isJunk']      = $props['folder'] == 'Junk' || $props['folder'] == 'Spam';
        $r['isNotJunk']   = !$r['isJunk'];
        $r['isAnswered']  = isset($r['answered']) && $r['answered'] ? true : false;
        $r['isRead']      = isset($r['seen']) && $r['seen'] ? true : false;
        $r['isForwarded'] = null;
        $r['isSeen']      = $r['isRead'];
        $r['isDeleted']   = isset($r['deleted']) && $r['deleted'] ? true : false;
        $r['status']      = array('imported');
        $r['permissions'] = array();
        $r['properties']  = null;
        
        
        $r['fromName'] = '';
        $r['fromEmail'] = '';
        $r['toName'] = array();
        $r['toEmail'] = array();
        
        $from = $p->getAddresses('from');
        if (count($from)) {
            if (isset($from[0]['display']) && $from[0]['display']) {
                $r['fromName'] = $from[0]['display'];
            }
            
            if (isset($from[0]['address']) && $from[0]['address']) {
                $r['fromEmail'] = $from[0]['address'];
            }
        }
        
        $tos = array_merge($p->getAddresses('to'), $p->getAddresses('cc'), $p->getAddresses('bcc'));
        foreach ($tos as $t) {
            if (isset($t['display']) && $t['display']) {
                $r['toName'][] = $t['display'];
            }
            if (isset($t['address']) && $t['address']) {
                $r['toEmail'][] = $t['address'];
            }
        }
        
//         if ($r['fromName'])
//             $r['text'][] = $r['fromName'];
//         if ($r['fromEmail'])
//             $r['text'][] = $r['fromEmail'];
        
//         foreach($r['toName'] as $tn) {
//             $r['text'][] = $tn;
//         }
//         foreach($r['toEmail'] as $tn) {
//             $r['text'][] = $tn;
//         }

        unset( $p );

        return $r;
    }
    
    public function queueEml($emlFile) {
        $r = $this->parseEml( $emlFile );
        
        $this->documents[] = $r;
        $this->documentCount++;
    }
    
    
    protected function lookupContent($message, $contentType='text/html') {
        if ($message->getContentType() == $contentType) {
            return $message->getContents();
        }
        
        foreach($message->getParts() as $p) {
            $c = $this->lookupContent($p);
            
            if ($c) {
                return $c;
            }
        }
        
        return null;
    }
    
    public function purge($force=false) {
        // only purge when there are more then 100 docs
        if ($force == false && count($this->documents) < 100) {
            return false;
        }
        
        // print "Purging..\n";
        $r = post_url($this->solrUrl . '/update', json_encode($this->documents), array(
            'headers' => array('Content-type: application/json')
        ));
        $json = json_decode($r);
        
        if (is_cli()) {
            print "Document no: " . $this->documentCount . PHP_EOL;
        }
        $this->commit();
        
        if (is_object($json) == false) {
            throw new SolrException( 'Invalid Solr response: ' . $r );
        }
        
        if ($json->responseHeader->status != 0) {
            throw new SolrException( $json->error->msg );
        }
        
        unset($this->documents);
        $this->documents = array();
    }
    
    
    public function postSolr($force=false) {
        if (count($this->documents) >= 100 || $force) {
            // print "Purging..\n";
            $r = post_url($this->solrUrl . '/update', json_encode($this->documents), array(
                'headers' => array('Content-type: application/json')
            ));
            $json = json_decode($r);
            
            if (is_cli()) {
                print "Document no: " . $this->documentCount . PHP_EOL;
            }
            $this->commit();
            
            if (is_object($json) == false) {
                throw new SolrException( 'Invalid Solr response: ' . $r );
            }
            
            if ($json->responseHeader->status != 0) {
                throw new SolrException( $json->error->msg );
            }
            
            $this->documents = array();
        }
    }
    
    
    
    public function commit() {
        get_url($this->solrUrl . '/update?commit=true');
    }
    
    public function truncate() {
        $d = post_url($this->solrUrl . '/update?commit=true', '<delete><query>*:*</query></delete>', array(
            'headers' => array('Content-type: text/xml')
        ));
        
    }
    
    
}


