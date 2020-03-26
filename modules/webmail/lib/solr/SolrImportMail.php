<?php


namespace webmail\solr;

use core\exception\SolrException;
use core\parser\HtmlParser;


class SolrImportMail {
    
    protected $contextName;
    protected $solrUrl;
    
    protected $documents = array();
    protected $documentCount = 1;
    
    protected $updateMode = false;                      // false = post EVERYTHING to solr. true = new or .properties changed?
    protected $emlFilesUpdate = array();
    
    public function __construct($solrUrl) {
        $this->contextName = \core\Context::getInstance()->getContextName();
        
        $this->setSolrUrl( $solrUrl );
        
    }
    
    public function setSolrUrl($url) { $this->solrUrl = $url; }
    public function getSolrUrl() { return $this->solrUrl; }
    
    public function setUpdateMode($bln) { $this->updateMode = $bln; }
    public function updateMode() { return $this->updateMode; }
    
    
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
    
    public function getPropertiesChecksum($emlFile) {
        $pf = $emlFile . '.properties';
        
        if (file_exists($pf)) {
            return crc32_int32( file_get_contents($pf) );
        } else {
            return -1;
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
        
        // use cleanup_string(), else solr might not accept document. In that case, the whole document-batch is rejected!
        $r['subject'] = cleanup_string( $p->getHeader('subject') );
        
        $r['content'] = $p->getMessageBody('html');
        
        
        
        $hp = new HtmlParser();
        $hp->loadString( $r['content'] );
        $hp->parse();
        
        // use cleanup_string(), else solr might not accept document. In that case, the whole document-batch is rejected!
        $r['content'] = cleanup_string( $hp->getBodyText() );
        
//         $r['text'] = array();
        
        if (trim($r['content']) == '') {
            $r['content'] = cleanup_string( $p->getMessageBody('text') );
//             $r['text'][] = $r['content'];
        }
        
        // fields
        // TODO: check out why 'folder' is not set in some cases?
        $r['mailboxName'] = isset($props['folder']) ? $props['folder'] : '';
        $r['isJunk']      = isset($props['folder']) && ($props['folder'] == 'Junk' || $props['folder'] == 'Spam');
        $r['isNotJunk']   = !$r['isJunk'];
        $r['isAnswered']  = isset($r['answered']) && $r['answered'] ? true : false;
        $r['isRead']      = isset($r['seen']) && $r['seen'] ? true : false;
        $r['isForwarded'] = null;
        $r['isSeen']      = $r['isRead'];
        $r['isDeleted']   = isset($r['deleted']) && $r['deleted'] ? true : false;
        $r['status']      = array('imported');
        $r['permissions'] = array();
        $r['properties_checksum']  = $this->getPropertiesChecksum($emlFile);
        
        
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
    
    public function updateEml( $emlFile, $force=false ) {
        if ($emlFile) {
            $this->emlFilesUpdate[] = realpath($emlFile);
        }
        
        if ($force || count($this->emlFilesUpdate) > 50) {
            // queue only files that must be updated
            // lookup properties_checksum for alle eml-files
            $q = '';
            foreach($this->emlFilesUpdate as $emlFile) {
                $id = substr($emlFile, strlen(ctx()->getDataDir()));
                
                if ($q != '')
                    $q = $q . ' OR ';
                $q = $q . " id:".solr_escapePhrase($id);
            }
            
            $sq = new SolrMailQuery();
            $sq->setRawQuery($q);
            $sq->addField('id');
            $sq->addField('properties_checksum');
            $sq->setRows(count($this->emlFilesUpdate));
            $smqr = $sq->search();
            
            // put it in a hash
            $docs = $smqr->getDocuments();
            $map_docs = array();
            foreach($docs as $d) {
                $map_docs[$d->id] = $d->properties_checksum;
            }
            
            // queue changed/"new"
            foreach($this->emlFilesUpdate as $emlFile) {
                $id = substr($emlFile, strlen(ctx()->getDataDir()));
                
                if (isset($map_docs[$id]) == false) {
//                     print "+ $id\n";
                    $this->queueEml( $emlFile );
                }
                else if ($this->getPropertiesChecksum($emlFile) != $map_docs[$id]) {
//                     print "- $id\n";
                    $this->queueEml( $emlFile );
                }
            }
            
            $this->emlFilesUpdate = array();
        }
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
        
        // forced & no documents? => skip
        if (count($this->documents) == 0) {
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
    
    
    
    public function commit() {
        get_url($this->solrUrl . '/update?commit=true');
    }
    
    public function truncate() {
        $d = post_url($this->solrUrl . '/update?commit=true', '<delete><query>*:*</query></delete>', array(
            'headers' => array('Content-type: text/xml')
        ));
    }
    
    
    public function importFolder( $dir ) {
        $files = list_files($dir, ['fileonly' => true, 'recursive' => true]);
        
        if (is_array($files)) for($x=0; $x < count($files); $x++) {
            if (file_extension($files[$x]) == 'eml') {
                
                if ($this->updateMode) {
                    $this->updateEml( $dir . '/' . $files[$x] );
                } else {
                    $this->queueEml( $dir . '/' . $files[$x] );
                }
                
                // purge handles minimum docs
                $this->purge();
            }
        }
        
        // empty update-queue
        if ($this->updateMode) {
            $this->updateEml(null, true);
        }
   
        $this->purge( true );
        $this->commit();
    }
    
}


