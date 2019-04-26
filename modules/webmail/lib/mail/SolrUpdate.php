<?php

namespace webmail\mail;



use core\exception\SolrException;

class SolrUpdate {
    
    protected $contextName;
    protected $datadir;
    protected $solrUrl = null;
    
    protected $documents = array();
    
    protected $documentCount=0;
    
    
    public function __construct($solrUrl=null, $contextName=null) {
        
        if ($solrUrl) {
            $this->solrUrl = $solrUrl;
        } else {
            $this->solrUrl = WEBMAIL_SOLR;
        }
        
        if ($contextName) {
            $this->contextName = $contextName;
        } else {
            $this->contextName = \core\Context::getInstance()->getContextName();
        }
        
        $this->datadir = \core\Context::getInstance()->getDataDir();
    }
    
    
    public function importFolder($folder, $recursive = true, $cnt=0) {
        $dh = opendir($folder);
        while ($f = readdir($dh)) {
            if ($f == '.' || $f == '..') continue;

            $path = realpath( $folder . '/' . $f );
            
            if ($recursive == false && is_dir($path)) continue;
            
            if ($recursive && is_dir($path)) {
                $this->importFolder($path, $recursive, $cnt + 1);
            }
            
            if (is_file($path)) {
                
//                 print "Queing doc: $path\n";
                $this->queueFile( $path );
                
                $this->purge();
            }
        }
        
        // end reached? => forge purge
        if ($cnt == 0) {
            $this->purge( true );
            $this->commit();
        }
    }
    
    public function parseEml($file) {
        $p = new \PhpMimeMailParser\Parser();
        $p->setPath($file);
        
        
        $r = array();
        $r['id'] = str_replace($this->datadir, '', $file);
        $r['contextName'] = $this->contextName;
        $r['file'] = str_replace($this->datadir, '', $file);
        
        $dt = new \DateTime(null, new \DateTimeZone('+0000'));
        $dt->setTimestamp(strtotime($p->getHeader('date')));
        $r['date'] = $dt->format('Y-m-d').'T'.$dt->format('H:i:s').'Z';
        $r['subject'] = $p->getHeader('subject');
        
        
        $r['content'] = $p->getMessageBody('html');
        if (!trim(strip_tags($r['content'])))
            $r['content'] = $p->getMessageBody('text');
        
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
        
        return $r;
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
        if (count($this->documents) >= 100 || $force) {
//             print "Purging..\n";
            $r = post_url($this->solrUrl . '/update', json_encode($this->documents), array(
                'headers' => array('Content-type: application/json')
            ));
            $json = json_decode($r);
            
            if (is_cli()) {
                print "Document no: " . $this->documentCount . PHP_EOL;
            }
            $this->commit();
            
            if (is_object($json) == false) {
                throw new SolrException( $r );
            }
            
            if ($json->responseHeader->status != 0) {
                throw new SolrException( $json->error->msg );
            }
            
            $this->documents = array();
        }
    }
    
    public function queueFile($path) {
        $r = $this->parseEml( $path );
        $this->queueDocument($r);
    }
    
    public function queueDocument($doc) {
        $this->documentCount++;
        $this->documents[] = $doc;
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
