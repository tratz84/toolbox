<?php


namespace webmail\storage;

use core\exception\SolrException;
use core\parser\HtmlParser;
use webmail\mail\MailProperties;
use core\db\solr\SolrQueryResponse;
use webmail\model\EmailDAO;
use core\exception\NotImplementedException;
use webmail\model\Email;
use webmail\model\EmailTo;


class MysqlImportMail extends ImportMailBase {
    
    protected $contextName;
    protected $solrUrl;
    
    protected $documents = array();
    protected $documentCount = 1;
    
    protected $updateMode = false;                      // false = post EVERYTHING to solr. true = new or .properties changed?
    protected $emlFilesUpdate = array();
    
    protected $forcedAction = null;
    
    protected $lastInReplyTo = null;
    
    
    public function __construct() {
        $this->contextName = \core\Context::getInstance()->getContextName();
        
    }
    
    public function setUpdateMode($bln) { $this->updateMode = $bln; }
    public function updateMode() { return $this->updateMode; }
    
    public function setForcedAction($a) { $this->forcedAction = $a; }
    
    public function getLastInReplyTo() { return $this->lastInReplyTo; }
    

    

    
    
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
            
            $mailIds = array();
            $q = '';
            foreach($this->emlFilesUpdate as $emlFile) {
                $id = substr($emlFile, strlen(ctx()->getDataDir()));
                
                $mailIds[] = addslashes( $id );
            }
            
            $eDao = new EmailDAO();
            $emails = $eDao->readBySolrMailId( $mailIds );
            
            
            foreach($emails as $e) {
                $map_docs[$e->getSolrMailId()] = $e->getServerPropertiesChecksum();
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
    
    public function purge($force=false, $opts=array()) {
        // only purge when there are more then 100 docs
        if ($force == false && count($this->documents) < 100) {
            return false;
        }
        
        // forced & no documents? => skip
        if (count($this->documents) == 0) {
            return false;
        }
        
        $mailIds = array();
        foreach($this->documents as $e) {
            $mailIds[] = $e['id'];
        }
        $eDao = new EmailDAO();
        $emails = $eDao->readBySolrMailId( $mailIds );
        $mapEmails = array();
        foreach( $emails as $e ) {
            $mapEmails[$e->getSolrMailId()] = $e;
        }
        
        foreach($this->documents as $e) {
            $email = null;
            if (isset($mapEmails[ $e['id'] ])) {
                $email = $mapEmails[ $e['id'] ];
            }
            else {
                $email = new Email();
                $email->setIncoming( true );
            }
            
            $mp = new MailProperties( $e['file'] );
//             $email->setUserId( ... );
//             $email->setCompanyId( ... );
//             $email->setPersonId( ... );
//             $email->setIdentityId( ... );

            $email->setConnectorId( $e['connectorId'] );
            $email->setConnectorImapfolderId( $this->lookupImapFolderId($e['connectorId'], $e['mailboxName']) );
            
            // build attributes
            $att = 0;
            if ($e['isSeen'] || $e['isRead'])
                $att |= Email::ATTRIBUTE_SEEN;
            if ($e['isAnswered'])
                $att |= Email::ATTRIBUTE_REPLIED;
            if ($e['isForwarded'])
                $att |= Email::ATTRIBUTE_FORWARDED;
            if ($e['isDeleted'])
                $att |= Email::ATTRIBUTE_DELETED;
            if ($e['isJunk'] || $e['isNotJunk'] == false)
                $att |= Email::ATTRIBUTE_SPAM;
            $email->setAttributes($att);
            
            $email->setMessageId( $e['emlMessageId'] );
            $email->setAction( $mp->getAction() );
//             $email->setSpam( false );
//             $email->setIncoming( true );
            $email->setFromName( $e['fromName'] );
            $email->setFromEmail( $e['fromEmail'] );
            
            if (strlen($e['subject']) > 512)
                $e['subject'] = substr($e['subject'], 0, 512);
            $email->setSubject( $e['subject'] );
            $email->setTextContent( $e['content'] );
//             $email->setReceived( true );
            $email->setDeleted( null );
            $email->setStatus( Email::STATUS_RECEIVED );
            
            if (valid_datetime($e['date']))
                $email->setCreated( format_date($e['date'], 'Y-m-d H:i:s') );
            else
                $email->setCreated(date('Y-m-d H:i:s'));
            
            $email->setSolrMailId( $e['id'] );
            $email->setConfidential( false );
            $email->setServerPropertiesChecksum( $e['server_properties_checksum'] );
            
            $isNew = $email->isNew();
            $email->save();
            
            // new only, recipients never change
            if ($isNew) {
                $emailRecipients = array();
                foreach( $e['recipients'] as $r ) {
                    $et = new EmailTo();
                    $et->setEmailId( $email->getEmailId() );
                    $et->setToType( $r['to_type'] );
                    $et->setToName( $r['to_name'] );
                    $et->setToEmail( $r['to_email'] );
                    $et->save();
                    
                    $emailRecipients[] = $et;
                }
                
                // set index
                $email->setReceived($emailRecipients);
                $email->updateTextSearch();
            }
            
        }
        
        if (is_cli()) {
            print_info("Document no: " . $this->documentCount);
        }
        
        unset($this->documents);
        $this->documents = array();
    }
    
    
    public function updateDoc($id, $doc) {
        
        $eDao = new EmailDAO();
        $email = $eDao->readBySolrMailId( $id );
        
        if ( $email ) {
            $this->documents[] = $doc;
            $this->purge( true );
            
            return true;
        }
        
        return false;
    }
    
    
    public function commit( $opts=array() ) {
//         throw new NotImplementedException('todo');
    }
    
    public function delete($rawQuery) {
        throw new NotImplementedException('todo: '.$rawQuery);
    }
    
    
    public function truncate() {
        // delete from webmail__email where connector_id is not null ?
        throw new NotImplementedException('todo');
    }
    
    
    
}


