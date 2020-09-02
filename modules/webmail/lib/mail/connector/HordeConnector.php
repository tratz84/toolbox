<?php



<?php


namespace webmail\mail\connector;

use core\ObjectContainer;
use webmail\mail\MailProperties;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrMail;


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
            $this->connectionOptions[] = 'ssl';
        } else {
            $secure = 'tls'
        }
        
        
        try {
            $this->client = new Horde_Imap_Client_Socket(array(
                'username' => $this->connector->getUsername(),
                'password' => $this->connector->getPassword(),
                'hostspec' => $this->connector->getHostname(),
                'port' => $this->connector->getPort(),
                'secure' => $secure,
            ));
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
    
    public function listFolders() {
        $list = $client->listMailboxes('*');
        
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


