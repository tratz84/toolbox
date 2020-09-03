<?php


namespace webmail\mail\connector;

use Horde_Imap_Client_Socket;
use webmail\model\Connector;


class HordeConnector extends BaseMailConnector {
    
    protected $connectionOptions = array();
    
    protected $client = null;
    
    protected $errors = null;
    
    protected $callback_itemImported = null;
    
    protected $serverPropertyChecksums = null;
    
    protected $imapFetchListCount = -1;
    protected $imapFetchOverviewOptions = 0;
    
    // just import messages 'SINCE'
    protected $sinceUpdate = null;
    
    
    public function __construct(Connector $connector) {
        parent::__construct($connector);
        
    }
    
    
    public function connect() {
        if ($this->connector->getPort() == 993) {
            $secure = 'ssl';
        } else {
            $secure = 'tls';
        }
        
        
        try {
            
            $opts = array();
            $opts['username'] = $this->connector->getUsername();
            $opts['password'] = $this->connector->getPassword();
            $opts['hostspec'] = $this->connector->getHostname();
            $opts['port']     = $this->connector->getPort();
            $opts['secure']   = $secure;
            
            $this->client = new Horde_Imap_Client_Socket( $opts );
        } catch (\Exception $ex) {
            $this->errors = [
                $ex->getMessage()
            ];
            
            return false;
        }
        
        return true;
    }
    
    public function isConnected() {
        if ($this->client !== null) {
            return true;
        } else {
            return false;
        }
    }
    
    public function disconnect() {
        if ($this->client === null) return;
        
        $this->client->shutdown();
        
        $this->client = null;
    }
    
    
    public function setSinceUpdate($t) { $this->sinceUpdate = $t; }
    
    public function setCallbackItemImported($callback) {
        $this->callback_itemImported = $callback;
    }
    
    
    public function ping() {
        if ($this->client === null) return false;
        
        return true;
    }
    
    public function check() {
        return $this->client->check();
    }
    
    
    public function listFolders() {
        $list = $this->client->listMailboxes('*');
        
        $folders = array_keys($list);
        
        usort($folders, function($f1, $f2) {
            if ($f1 == 'INBOX') {
                return -1;
            }
            if ($f2 == 'INBOX') {
                return 1;
            }
            
            return strcmp($f1, $f2);
        });
        
        return $folders;
    }
    
    
    
    
}


