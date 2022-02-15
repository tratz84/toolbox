<?php


namespace webmail\solr;

use core\exception\SolrException;
use core\parser\HtmlParser;
use webmail\mail\MailProperties;
use core\db\solr\SolrQueryResponse;
use webmail\storage\ImportMailBase;


class SolrImportMail extends ImportMailBase {
    
    protected $contextName;
    protected $solrUrl;
    
    protected $documents = array();
    protected $documentCount = 1;
    
    protected $updateMode = false;                      // false = post EVERYTHING to solr. true = new or .properties changed?
    protected $emlFilesUpdate = array();
    
    protected $forcedAction = null;
    
    protected $lastInReplyTo = null;
    
    
    public function __construct( ) {
        $this->contextName = \core\Context::getInstance()->getContextName();
        
        
        if (defined('WEBMAIL_SOLR'))
            $this->setSolrUrl( WEBMAIL_SOLR );
    }
    
    public function setSolrUrl($url) { $this->solrUrl = $url; }
    public function getSolrUrl() { return $this->solrUrl; }
    
    public function setUpdateMode($bln) { $this->updateMode = $bln; }
    public function updateMode() { return $this->updateMode; }
    
    public function setForcedAction($a) { $this->forcedAction = $a; }
    
    public function getLastInReplyTo() { return $this->lastInReplyTo; }
    
    
    protected function attachmentCount(\PhpMimeMailParser\Parser $p) {
        $att = $p->getAttachments();
        
        $attachmentCount = 0;
        
        foreach($att as $at) {
            $attHeaders = $at->getHeaders();
            
//             if (isset($attHeaders['content-id'])) {
//                 // content-id set? => attachment used in e-mail. Don't count as attachment
//             }
//             else 
            if (isset($attHeaders['content-disposition']) && stripos($attHeaders['content-disposition'], 'attachment') !== false) {
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
        $r = $this->parseEml( $emlFile, ['solr_import' => true] );
        
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
            
            print_cli_info( "Document count: " . $this->documentCount );
        }
    }
    
    

    
    public function purge($force=false, $opts=array()) {
        // only purge when there are more then 100 docs
        if ($force == false && count($this->documents) < 100) {
            return false;
        }
        
        // forced & no documents? => skip
        if (count($this->documents) == 0) {
            return false;
        }
        
        print_cli_info('SolrImportMail::purge');
        
        // print "Purging..\n";
        $r = post_url($this->solrUrl . '/update', json_encode($this->documents), array(
            'headers' => array('Content-type: application/json')
        ));
        
        $json = json_decode($r);
        
        print_cli_info("Document no: " . $this->documentCount);
        
        $this->commit($opts);
        
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
            $this->purge(true, ['softCommit' => true]);
            
            return true;
        }
        
        return false;
    }
    
    
    public function commit( $opts=array() ) {
        if (isset($opts['softCommit']) && $opts['softCommit']) {
            get_url($this->solrUrl . '/update?softCommit=true');
        } else {
            get_url($this->solrUrl . '/update?commit=true');
        }
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


