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
    
    
    public function stop() {
    }
    
    public function poll() {
    }
    
    public function import() {
    }
    
    public function expunge() {
    }
    
    public function emptyFolder($folderName) {
    }
}

