<?php


namespace webmail\solr;

use core\exception\SolrException;
use core\parser\HtmlParser;
use webmail\mail\MailProperties;
use core\db\solr\SolrQueryResponse;


class SolrImportMail {
    
    protected $contextName;
    protected $solrUrl;
    
    protected $documents = array();
    protected $documentCount = 1;
    
    protected $updateMode = false;                      // false = post EVERYTHING to solr. true = new or .properties changed?
    protected $emlFilesUpdate = array();
    
    protected $forcedAction = null;
    
    protected $lastInReplyTo = null;
    
    
    public function __construct($solrUrl=null) {
        if ($solrUrl == null) {
            $solrUrl = WEBMAIL_SOLR;
        }
        
        $this->contextName = \core\Context::getInstance()->getContextName();
        
        $this->setSolrUrl( $solrUrl );
        
    }
    
    public function setSolrUrl($url) { $this->solrUrl = $url; }
    public function getSolrUrl() { return $this->solrUrl; }
    
    public function setUpdateMode($bln) { $this->updateMode = $bln; }
    public function updateMode() { return $this->updateMode; }
    
    public function setForcedAction($a) { $this->forcedAction = $a; }
    
    public function getLastInReplyTo() { return $this->lastInReplyTo; }
    
    public function parseEml($emlFile) {
        $mp = new MailProperties($emlFile);
        $mp->load();
        
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
        $r['emlMessageId'] = cleanup_string( $p->getHeader('Message-ID') );
        $r['emlThreadId'] = cleanup_string( $p->getHeader('Thread-Index') );
        $r['refMessageId'] = $this->buildRefMessageIds( $p );
        $this->lastInReplyTo = $p->getHeader('In-Reply-To');
        
        $r['subject'] = cleanup_string( $p->getHeader('subject') );
        
        // html mail?
        $htmlMessageBody = $p->getMessageBody('html');
        if (trim( $htmlMessageBody ) != '') {
            $hp = new HtmlParser();
            $hp->loadString( $htmlMessageBody );
            $hp->parse();
            
            // use cleanup_string(), else solr might not accept document. In that case, the whole document-batch is rejected!
            $r['content'] = cleanup_string( $hp->getBodyText() );
        }
        // text mail?
        else {
            // use cleanup_string(), else solr might not accept document. In that case, the whole document-batch is rejected!
            $r['content'] = cleanup_string( $p->getMessageBody('text') );
        }
        
        
//         $r['text'] = array();
        
        if (trim($r['content']) == '') {
            $r['content'] = cleanup_string( $p->getMessageBody('text') );
//             $r['text'][] = $r['content'];
        }
        
        // fields
        // TODO: check out why 'folder' is not set in some cases?
        $r['mailboxName']          = $mp->getFolder();
        $r['connectorId']          = $mp->getConnectorId();
        $r['connectorDescription'] = $mp->getConnectorDescription();
        $r['isJunk']               = $mp->getFolder() == 'Junk' || $mp->getFolder() == 'Spam';
        $r['isNotJunk']            = !$r['isJunk'];
        $r['isAnswered']           = $mp->getAnswered() ? true : false;
        $r['isRead']               = $mp->getSeen() ? true : false;
        $r['isForwarded']          = null;
        $r['isSeen']               = $r['isRead'];
        $r['isDeleted']            = $mp->getDeleted() ? true : false;
        $r['status']               = array('imported');
        $r['permissions']          = array();
        $r['server_properties_checksum'] = MailProperties::checksumServerProperties($emlFile);
        
        $r['markDeleted']          = $mp->getMarkDeleted();
        $r['attachmentCount']      = $this->attachmentCount($p);
        
        $r['userId'] = $mp->getUserId();
        
        // forcedAction set?
        if ($this->forcedAction && $mp->getAction() != $this->forcedAction) {
            $mp->setAction( $this->forcedAction );
            $mp->save();
        }
        
        $r['action'] = $mp->getAction();
        
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
        

        unset( $p );

        return $r;
    }
    
    protected function attachmentCount(\PhpMimeMailParser\Parser $p) {
        $att = $p->getAttachments(true);
        
        $attachmentCount = 0;
        
        foreach($att as $at) {
            $attHeaders = $at->getHeaders();
            
            if (isset($attHeaders['content-id'])) {
                // content-id set? => attachment used in e-mail. Don't count as attachment
            }
            else if (isset($attHeaders['content-disposition']) && stripos($attHeaders['content-disposition'], 'attachment') !== false) {
                $attachmentCount++;
            }
            else if ($at->getContentDisposition() == 'attachment') {
                // not sure to count this one
                $attachmentCount++;
            }
        }
        
        return $attachmentCount;
    }
    
    
    /**
     * buildRefMessageIds() - returns array with all id's that reference this eml (In-Reply-To, References, Thread-Index)
     * 
     * @return string[]
     */
    protected function buildRefMessageIds(\PhpMimeMailParser\Parser $p) {
        $ids = array();
        
        $str = '';
        $str .= $p->getHeader('In-Reply-To') . "\n";
        $str .= $p->getHeader('References') . "\n";
        $str .= $p->getHeader('Thread-Index') . "\n";
        
        $raw_ids = preg_split("/(\n| )/", $str);
        foreach($raw_ids as $ri) {
            $ri = trim($ri);
            
            if ($ri && in_array($ri, $ids) == false) {
                $ids[] = $ri;
            }
        }
        
        return $ids;
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
            // lookup server_properties_checksum for all eml-files
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
            $sq->addField('server_properties_checksum');
            $sq->setRows(count($this->emlFilesUpdate));
            $smqr = $sq->search();
            
            // put it in a hash
            $docs = $smqr->getDocuments();
            $map_docs = array();
            foreach($docs as $d) {
                $map_docs[$d->id] = $d->server_properties_checksum;
            }
            
            // queue changed/"new"
            foreach($this->emlFilesUpdate as $emlFile) {
                $id = substr($emlFile, strlen(ctx()->getDataDir()));
                
                if (isset($map_docs[$id]) == false) {
//                     print "+ $id\n";
                    $this->queueEml( $emlFile );
                }
                else if (MailProperties::checksumServerProperties($emlFile) != $map_docs[$id]) {
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
    
    
    public function updateDoc($id, $doc) {
        $sq = new \core\db\solr\SolrQuery( $this->solrUrl );
        $sq->addFacetSearch('contextName', ':', ctx()->getContextName());
        $sq->addFacetSearch('id', ':', $id);
        
        /** @var SolrQueryResponse $sqr */
        $sqr = $sq->search();
        
        if ($sqr->getNumFound() == 1) {
            $docs = $sqr->getDocuments();
            $doc = array_merge((array)$docs[0], $doc);
            
            $this->documents[] = $doc;
            $this->purge(true);
            
            return true;
        }
        
        return false;
    }
    
    
    public function commit() {
        get_url($this->solrUrl . '/update?commit=true');
    }
    
    public function delete($rawQuery) {
        post_url($this->solrUrl . '/update?commit=true', '<delete><query>'.$rawQuery.'</query></delete>', array(
            'headers' => array('Content-type: text/xml')
        ));
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


