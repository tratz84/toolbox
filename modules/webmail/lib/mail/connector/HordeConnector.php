<?php


namespace webmail\mail\connector;

use Horde_Imap_Client_Socket;
use webmail\mail\MailProperties;
use webmail\model\Connector;
use webmail\solr\SolrMail;
use core\exception\OutOfBoundException;
use core\exception\InvalidStateException;


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
            
            $opts['debug'] = '/home/timvw/tmp/horde-log';
            
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
    
    
    protected function buildOverviewList( $folderName, $messageCount ) {
        $list = array();
        
        if ($this->sinceUpdate) {
            $q = new \Horde_Imap_Client_Search_Query();
            
            $q->dateSearch( new \DateTime($this->sinceUpdate), \Horde_Imap_Client_Search_Query::DATE_SINCE );
            
            $opts = array();
            $opts['sort'] = array(\Horde_Imap_Client::SORT_ARRIVAL);//, \Horde_Imap_Client::SORT_REVERSE);
            $uids = $this->client->search( $folderName, $q, $opts );
            $uids = $uids['match']->ids;
            
            $this->imapFetchListCount = count($uids);
            
            $chunked_uids = $uids ? array_chunk($uids, 100) : array();
            
            foreach($chunked_uids as $cuid) {
                $list[] = new \Horde_Imap_Client_Ids($cuid);
            }
        } else {
            $q = new \Horde_Imap_Client_Search_Query();
            
            $opts = array();
            $opts['sort'] = array(\Horde_Imap_Client::SORT_ARRIVAL);//, \Horde_Imap_Client::SORT_REVERSE);
            $uids = $this->client->search( $folderName, $q, $opts );
            $uids = $uids['match']->ids;
            
            $this->imapFetchListCount = count($uids);
            
            $chunked_uids = $uids ? array_chunk($uids, 100) : array();
            
            foreach($chunked_uids as $cuid) {
                $list[] = new \Horde_Imap_Client_Ids($cuid);
            }
        }
        
        return $list;
    }
    
    
    protected function determineEmailPath(\Horde_Imap_Client_Data_Fetch $fetch) {
        $dt = $fetch->getImapDate();
        $dt->setTimezone(new \DateTimeZone('+0000'));
        
        $env = $fetch->getEnvelope();
        
        $subject    = $env->subject;
        $from       = (string)$env->from->first();            // this one is prolly not 1on1 with imap_fetch's result..
        $message_id = (string)$env->message_id;
        $subject    = (string)$env->subject;
        $udate      = $dt->getTimestamp();
        $size       = $fetch->getSize();
        
        $uid = @md5($size . $message_id . $from . $subject . $udate);
        
        $p = ctx()->getDataDir() . '/webmail/inbox/' . $dt->format('Y') . '/' . $dt->format('m') . '/' . $dt->format('d');
        
        $file = $p . '/' . $uid . '.eml';
        
        return $file;
    }
    
    
    public function buildMessageProperties($emlFile, $folderName, \Horde_Imap_Client_Data_Fetch $fetch) {
        $dt = $fetch->getImapDate();
        
        $env = $fetch->getEnvelope();
        
        // put flags in array
        $flags = array();
        foreach( $fetch->getFlags() as $f ) {
            $f = strtolower(ltrim($f, '\\'));
            $flags[$f] = true;
        }
        
        // $mp = message-properties
        $mp = new MailProperties($emlFile);
        $mp->setServerProperty('connectorId',          $this->connector->getConnectorId());
        $mp->setServerProperty('connectorDescription', $this->connector->getDescription());
        
        
        $mp->setServerProperty('folder',      $folderName);
        $mp->setServerProperty('subject',     @$env->subject);
        $mp->setServerProperty('from',        (string)@$env->from->first());
        $mp->setServerProperty('to',          (string)@$env->to->first());
        $mp->setServerProperty('size',        $fetch->getSize());
        $mp->setServerProperty('message_id',  @$env->message_id);
        $mp->setServerProperty('uid',         @$fetch->getUid());
        $mp->setServerProperty('udate',       @$fetch->getImapDate()->getTimestamp());
        
        $mp->setServerProperty('flagged',     isset($flags['flagged'])  ? 1 : 0);
        $mp->setServerProperty('answered',    isset($flags['answered']) ? 1 : 0);
        $mp->setServerProperty('deleted',     isset($flags['deleted'])  ? 1 : 0);
        $mp->setServerProperty('seen',        isset($flags['seen'])     ? 1 : 0);
        $mp->setServerProperty('draft',       isset($flags['draft'])    ? 1 : 0);
        
        $mp->setToolboxProperty('created',    date(\DateTime::ISO8601));
        
        if ($mp->toolboxPropertyFileExists() == false && $mp->getProperty('action') == '') {
            if ($mp->getAnswered() && ($mp->getAction() == '' || $mp->getAction() == 'open')) {
                // maybe also do this for ACTION_URGENT ?
                $mp->setAction(SolrMail::ACTION_REPLIED);
            } else if ($folderName == 'Sent') {
                $mp->setAction(SolrMail::ACTION_DONE);
            } else {
                $mp->setAction(SolrMail::ACTION_OPEN);
            }
        }
        
        return $mp;
    }
    
    
    protected function saveMessage($folderName, \Horde_Imap_Client_Data_Fetch $fetch) {
        $file = $this->determineEmailPath($fetch);
        
        if (is_dir(dirname($file)) == false) {
            if (mkdir(dirname($file), 0755, true) == false) {
                return false;
            }
        }
        
        $mp = $this->buildMessageProperties($file, $folderName, $fetch);
        
        // TODO: props changed..?
        $mp->save();
        
        // mail/eml itself won't ever change. Only it's overhead (that's put into the .properties-file)
        if (file_exists($file)) {
            return false;
        }
        
        $q = new \Horde_Imap_Client_Fetch_Query();
        $q->fullText();
        $q->headerText();
//         $q->envelope();
        
        $opts = array();
        $opts['ids'] = new \Horde_Imap_Client_Ids( $fetch->getUid() );
        
        $results = $this->client->fetch( $folderName, $q, $opts );
        
        $f = $results->first();

        // ??
        if (!$f)
            throw new OutOfBoundException('Mail not found');
        
        $str = $f->getFullMsg();
        
        if (is_cli()) {
            print_info("Saving e-mail to file: $file");
        }
        
        $fh = fopen($file, 'w');
        if (!$fh) {
            print_info("ERROR: Unable to open file: $file");
            return false;
        }
        
        $r = fwrite($fh, $str);
        if ($r != strlen($str)) {
            // TODO: handle this?  skip? disk full?
            throw new InvalidStateException( 'Writing of file failed, disk full? "'.$str.'"' );
        }
        
        fclose($fh);
        
        return $file;
    }
    
    
    public function importItems($folderName) {
        
        try {
            $this->client->openMailbox($folderName);//, \Horde_Imap_Client::OPEN_READONLY);
        } catch (\Exception $ex) {
            return false;
        }
//             return false;
        
        $r = $this->client->status( $folderName );
        $messageCount = $r['messages'];
        
//         var_export($r);exit;
        
        $items = array();
        
        // Fetch an overview for all messages in INBOX
        $overviewList = $this->buildOverviewList( $folderName, $messageCount );
        
        // fetch messages
        $x=0;
        foreach($overviewList as $range) {
            
            $q = new \Horde_Imap_Client_Fetch_Query();
            $q->flags();
            $q->envelope();
            $q->size();
            $q->uid();
            $q->imapDate();
            
            $opts = array();
            $opts['ids'] = $range;
            
            $results = $this->client->fetch( $folderName, $q, $opts );
            $ids = $results->ids();
            
            if (is_cli()) {
                print_info("Importing msg: " . $folderName . " (" . $x . "/" . $this->imapFetchListCount . ')');
            }
            
            for($y=0; $y < count($ids); $y++) {
                /** @var \Horde_Imap_Client_Data_Fetch $fetch */
                $fetch = $results->get( $ids[$y] );
                
                $emlfile = $this->determineEmailPath( $fetch );
                
                // INBOX has special business rules. Skip update if e-mail is not yet imported by bin/webmail_connector.php
                if ($folderName == 'INBOX' && file_exists($emlfile) == false) {
                    continue;
                }
                
                $mp = $this->buildMessageProperties($emlfile, $folderName, $fetch);
                
                // check if mail (properties) are changed
                $changed = $this->serverPropertiesChanged( $emlfile, $mp );
                
                if ($changed) {
                    $mp->save();
                    
                    // TODO: if Folder = Sent, check 'In-Reply-To'-header & lookup replied e-mail. If status == 'open', set to REPLIED
                    $this->saveMessage($folderName, $fetch);
                }
                
                // callback (probably Solr-import)
//                 call_user_func($this->callback_itemImported, $folderName, $results[$y], $emlfile, $changed);
            }
            
            
            $x += count($results);
        }
        
        return $items;
    }
    
    public function doImport(Connector $c) {
        
        $folders = $c->getImapfolders();
        
        // put 'Sent'-folder on bottom. 'REPLIED'-flag is set based on sent-
        // messages by looking at the 'In-Reply-To'-header. To be able to
        // do this the parent message must be imported/synced first
        usort($folders, function($o1, $o2) use ($c) {
            if ($o1->getConnectorImapfolderId() == $c->getSentConnectorImapfolderId())
                return 1;
                if ($o2->getConnectorImapfolderId() == $c->getSentConnectorImapfolderId())
                    return -1;
                    
                    return strcmp($o1->getFolderName(), $o2->getFolderName());
        });
        
        
        foreach($folders as $if) {
            if (!$if->getActive()) {
                continue;
            }
            
            if ($if->getFolderName() != 'Afgehandeld') continue;
            
            if (is_cli())
                print_info("Importing: " . $if->getFolderName());
            
            $this->importItems( $if->getFolderName() );
        }
    }
    
    
}


