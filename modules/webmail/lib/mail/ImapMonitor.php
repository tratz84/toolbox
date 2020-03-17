<?php

namespace webmail\mail;

use webmail\model\Connector;

class ImapMonitor extends MailMonitor {
    
    protected $imapConnection = null;
    
    protected $check = null;
    
    protected $connected = false;
    protected $lastTimeTriedToConnect = null;
    
    protected $callback_itemImported = null;
    
    
    public function __construct(Connector $connector) {
        $this->setConnector( $connector );
        
    }
    
    public function setCallbackItemImported($callback) { $this->callback_itemImported = $callback; }
    
    public function connect() {
        if ($this->imapConnection == null) {
            $this->imapConnection = \webmail\mail\ImapConnection::createByConnector($this->connector);
            $this->imapConnection->setCallbackItemImported($this->callback_itemImported);
        }
        
        // try to (re)connect max once every 60-seconds
        if ($this->lastTimeTriedToConnect == null || (time()-60) > $this->lastTimeTriedToConnect) {
            $this->connected = $this->imapConnection->connect();
            $this->lastTimeTriedToConnect = time();
            return $this->connected;
        } else {
            return false;
        }
    }
    
    public function disconnect() {
        $this->imapConnection->disconnect();
        $this->imapConnection = null;
        $this->connected = false;
    }
    
    public function stop() {
        parent::stop();
        $this->disconnect();
        $this->setCallbackItemImported(null);
    }
    
    
    /**
     * poll() - checks if there's new mail
     * 
     * @return boolean true/false, true if there's new mail
     */
    public function poll() {
        // no connection? => try to connect
        if ($this->imapConnection == null || $this->connected == false) {
            if (!$this->connect()) {
                return false;
            }
        }

        // fetch mailbox status
        $oldCheck = $this->check;
        $this->check = $this->imapConnection->check();
        
        $checkMailbox = false;
        
        // first run & check-succeeded? => return true
        if ($oldCheck == null && is_object($this->check)) {
            $checkMailbox = true;
        }
        // ..nd-run? => compare with previous response
        if (is_object($oldCheck) && is_object($this->check) && $oldCheck->Nmsgs != $this->check->Nmsgs) {
            $checkMailbox = true;
        }
        
        if ($checkMailbox)
            return true;
        
        // check-failed? => disconnect
        if (!$this->check) {
            $this->disconnect();
        }
        
        return false;
    }
    
    
    
    public function import() {
        $items = $this->imapConnection->importInbox( $this->connector );
    }
    
    
}


