<?php


namespace webmail\mail\connector;

use core\exception\InvalidStateException;
use webmail\model\Connector;


abstract class BaseMailConnector {
    
    protected $connector;
    protected $blnRunning = true;
    
    
    public function __construct(Connector $connector) {
        $this->setConnector( $connector );
    }
    
    
    public function setConnector($c) { $this->connector = $c; }
    public function getConnector() { return $this->connector; }
    
    
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
    public function lookupUid($folder, $solrMail) { }
    
    public function appendMessage($folder, $emlMessage) { }
    
}

