<?php


namespace webmail\mail\connector;

use core\exception\InvalidStateException;
use webmail\mail\MailProperties;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrMail;


abstract class BaseMailConnector {
    
    protected $connector;
    protected $blnRunning = true;
    
    protected $serverPropertyChecksums = null;
    
    protected $lastError = null;
    
    public function __construct(Connector $connector) {
        $this->setConnector( $connector );
    }
    
    
    public function setConnector($c) { $this->connector = $c; }
    public function getConnector() { return $this->connector; }
    
    public function getLastError() { return $this->lastError; }
    
    
    public static function createMailConnector(Connector $connector) {
        if ($connector->getConnectorType() == 'imap') {
            $ic = new ImapConnector($connector);
            return $ic;
        }
        else if ($connector->getConnectorType() == 'horde') {
            $hc = new HordeConnector( $connector );
            return $hc;
        }
        else if ($connector->getConnectorType() == 'pop3') {
            $pc = new Pop3Connector($connector);
            return $pc;
        }
        else {
            throw new InvalidStateException('Invalid connectorType');
        }
    }
    
    public abstract function connect();
    public abstract function disconnect();
    
    
    // check if there's new mail
    public function poll() { }
    
    // import mail
    public function import() { }
    
    // expunge deleted
    public function expunge() { }
    
    // empty folder
    public function emptyFolder($folderName) { }
    
    
    public function markMail($uid, $folder, $flag) { }
    public function markJunk($uid, $folder) { }
    
    public function moveMailByUid($uid, $srcFolder, $dstFolder) { }
    public function deleteMailByUid($uid, $folder) { }
    public function lookupUid($folder, SolrMail $solrMail) { }
    
    public function appendMessage($folder, $emlMessage) { }
    
    
    
    
    public function serverPropertiesChanged($emlfile, MailProperties $data) {
        $chksum = crc32_int32(serialize($data->getServerProperties()));
        
        if ($this->serverPropertyChecksums === null) {
            $f = get_data_file('webmail/message-checksums');
            if ($f) {
                $this->serverPropertyChecksums = unserialize( file_get_contents( $f ) );
            }
            
            if ($this->serverPropertyChecksums == false) {
                $this->serverPropertyChecksums = array();
            }
        }
        
        if (isset($this->serverPropertyChecksums[ $emlfile ]) && $this->serverPropertyChecksums[ $emlfile ] == $chksum) {
            return false;
        }
        
        $this->serverPropertyChecksums[ $emlfile ] = $chksum;
        
        return true;
    }
    
    public function saveServerPropertyChecksums() {
        
        return save_data('webmail/message-checksums', serialize($this->serverPropertyChecksums));
    }
    
    
    
    
    protected function applyFilters($connector, $file, $messageUid) {
        $isSpam = false;
        
        $p = new \PhpMimeMailParser\Parser();
        $p->setPath($file);
        
        $filters = $connector->getFilters();
        
        foreach($filters as $f) {
            // skip inactive filters
            if ($f->getActive() == false)
                continue;
            
            $conditions = $f->getConditions();
            
            $conditionCount = 0;
            foreach($conditions as $c) {
                if ( $c->match($p, $file) ) {
                    if ($c->getFilterType() == 'is_spam') {
                        $isSpam = true;
                    }
                    
                    $conditionCount++;
                }
            }
            
            if (($f->getMatchMethod() == 'match_all' && $conditionCount == count($conditions)) || ($f->getMatchMethod() == 'match_one' && $conditionCount > 0)) {
                $actions = $f->getActions();
                
                if (count($actions) == 0)
                    return null;
                
                $return_value = array();
                $return_value['is_spam'] = $isSpam;
                
                $moveFolderActionValue = null;
                foreach($actions as $action) {
                    if ($action->getFilterAction() == 'move_to_folder') {
                        $moveFolderActionValue = $action->getFilterActionValue();               // this is an webmail__connector_imapfolder.connector_imapfolder_id
                    }
                    if ($action->getFilterAction() == 'set_action') {
                        $return_value['set_action'] = $action->getFilterActionValue();
                    }
                }
                
                if ($moveFolderActionValue) {
                    /** @var ConnectorService $connectorService */
                    $connectorService = object_container_getget(ConnectorService::class);
                    $f = $connectorService->readImapFolder( $moveFolderActionValue );
                    
                    // found? => move
                    if ($f) {
                        if ($isSpam) {
                            $this->markJunk($messageUid, 'INBOX');
                        }
                        
                        
                        if ( $this->moveMailByUid($messageUid, 'INBOX', $f->getFolderName()) ) {
                            
                        }
                        
                        $return_value['move_to_folder'] = $f->getFolderName();
                    }
                }
                
                return $return_value;
            }
        }
        
        return array();
    }
    
}

