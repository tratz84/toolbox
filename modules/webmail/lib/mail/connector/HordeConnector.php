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
        
        try {
            $this->client->noop();
        } catch (\Horde_Imap_Client_Exception $ex) {
            return false;
        }
        
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
    
    
    protected function determineEmailPath( \Horde_Imap_Client_Data_Fetch $cdf ) {
        // ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Envelope.html
        $env = $cdf->getEnvelope();
        
        $overview = new \stdClass();
        $overview->size       = $cdf->getSize();
        $overview->udate      = $cdf->getImapDate()->format('U');
        $overview->subject    = $env->subject;
        $overview->from       = $env->from->writeAddress();
        $overview->message_id = $env->message_id;
        
        
        $dt = new \DateTime();
        $dt->setTimestamp($overview->udate);
        $dt->setTimezone(new \DateTimeZone('+0000'));
        
        $uid = @md5($overview->size . $overview->message_id . $overview->from . $overview->subject . $overview->udate);
        
        $p = ctx()->getDataDir() . '/webmail/inbox/' . $dt->format('Y') . '/' . $dt->format('m') . '/' . $dt->format('d');
        
        $file = $p . '/' . $uid . '.eml';
        
        return $file;
    }
    
    
    public function importItems($folderName) {
        
       
        $searchQuery = new \Horde_Imap_Client_Search_Query();
//         $searchQuery->dateSearch(new \DateTime('2021-03-01 00:00:00'), \Horde_Imap_Client_Search_Query::DATE_SINCE);
        
        $results = $this->client->search( $folderName, $searchQuery );
//         var_export($results); return;
        
        $fetchQuery = new \Horde_Imap_Client_Fetch_Query();
        $fetchQuery->imapDate();
//         $fetchQuery->size();
//         $fetchQuery->uid();
        $fetchQuery->envelope();
        $fetchQuery->size();
        
        
        $list = $this->client->fetch( $folderName, $fetchQuery, array(
            'ids' => new \Horde_Imap_Client_Ids( $results['match'] )
        ));
        
        
        $ids = $list->ids();
        foreach($ids as $id) {
            // ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Fetch.html
            $cdf = $list->offsetGet( $id );
            
            // ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Envelope.html
            $env = $cdf->getEnvelope();
            
            
            $eml = $this->determineEmailPath( $cdf );

        }
        
        
        
    }
    
    
    
    
    
}


