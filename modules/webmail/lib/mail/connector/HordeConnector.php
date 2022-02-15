<?php
/**
 * HordeConnector() - imap connector through horde-classes
 * 
 * Examples @ https://github.com/wrobel/horde/blob/master/framework/Imap_Client/test/Horde/Imap/Client/test_client.php
 * 
 * 
 */


namespace webmail\mail\connector;

use core\exception\InvalidStateException;
use core\exception\OutOfBoundException;
use Horde_Imap_Client_Socket;
use webmail\mail\MailProperties;
use webmail\mail\render\MysqlMailRender;
use webmail\model\Connector;


class HordeConnector extends BaseMailConnector {
    
    protected $connectionOptions = array();
    
    protected $client = null;
    
    protected $errors = null;
    
    protected $check = null;
    
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
            
            if (is_debug()) {
                $opts['debug'] = ctx()->getDataDir() . '/horde-imap.log';
            }
            
            $this->client = new Horde_Imap_Client_Socket( $opts );
        } catch (\Exception $ex) {
            $this->errors = [
                $ex->getMessage()
            ];
            
            return false;
        }
        
        return true;
    }
    
    /**
     * getClient() - debug only, don't use for prod
     * @return \Horde_Imap_Client_Socket
     */
    public function getClient() {
        return $this->client;
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
    
    protected function handleCallbackItemImported( $folderName, \Horde_Imap_Client_Data_Fetch $result, $emlfile, $changd) {
        if ($this->callback_itemImported == null) {
            return;
        }
        
        $env = $result->getEnvelope();
        
        $opts = array();
        $opts['subject']= $env->subject;
        $opts['date'] = $result->getImapDate()->format('Y-m-d H:i:s');
        
        return call_user_func($this->callback_itemImported, $folderName, $opts, $emlfile, true);
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
    
    
    protected function buildOverviewList( $folderName ) {
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
                $mp->setAction(MysqlMailRender::ACTION_REPLIED);
            } else if ($folderName == 'Sent') {
                $mp->setAction(MysqlMailRender::ACTION_DONE);
            } else {
                $mp->setAction( MysqlMailRender::ACTION_OPEN );
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
        $q->fullText(array('peek' => true));            // peek = true, doesnt mark message as Seen
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
        $overviewList = $this->buildOverviewList( $folderName );
        
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
                $this->handleCallbackItemImported($folderName, $fetch, $emlfile, $changed);
            }
            
            
            $x += count($results);
        }
        
        return $items;
    }
    
    
    
    public function importInbox() {
        $items = array();
        
        
        $blnExpunge = false;
        
        // Fetch an overview for all messages in INBOX
        $q = new \Horde_Imap_Client_Fetch_Query();
        $q->flags();
        $q->envelope();
        $q->size();
        $q->uid();
        $q->imapDate();
        
        $opts = array();
        
        $results = $this->client->fetch( 'INBOX', $q, $opts );
        $ids = $results->ids();
        
        
        for($y=0; $y < count($ids); $y++) {
            $fetch = $results->get( $ids[$y] );
            
            if (in_array('\\Deleted', $fetch->getFlags()))
                continue;
            
            // save file locally
            $file = $this->determineEmailPath( $fetch );
            
            if (file_exists($file) == false) {
                $emlfile = $this->saveMessage('INBOX', $fetch);
                
                // new?
                if ($emlfile) {
                    // apply filters
                    print_info("Applying filters");
                    $result = $this->applyFilters($this->connector, $file, $ids[$y]);
                    
                    // update propertiesName
                    $mp = new MailProperties($emlfile);
                    $mp->load();
                    
                    // message moved to another folder?
                    if (isset($result['move_to_folder'])) {
                        // set new folder name
                        $mp->setFolder( $result['move_to_folder'] );
                        $folderName = $result['move_to_folder'];
                        
                        // call imap_expunge
                        $blnExpunge = true;
                    }
                    // set default folder
                    else {
                        $folderName = 'INBOX';
                    }
                    
                    // mark as spam
                    if (isset($result['is_spam']) && $result['is_spam']) {
                        $mp->setJunk( true );
                    }
                    
                    // set_action?
                    if (isset($result['set_action']) && $result['set_action']) {
                        $mp->setAction( $result['set_action'] );
                    }
                    
                    $mp->save();
                    
                    
                    // call callback
                    $this->handleCallbackItemImported($folderName, $fetch, $emlfile, true);
                }
            }
        }
        
        if ($blnExpunge) {
//             imap_expunge($this->imap);
        }
        
        return $items;
    }
    
    
    public function doImport() {
        
        $c = $this->connector;
        
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
            
            if (is_cli())
                print_info("Importing: " . $if->getFolderName());
            
            $this->importItems( $if->getFolderName() );
        }
    }
    
    
    
    public function markJunk($uid, $folderName) {
        $options = [
            'uids' => new \Horde_Imap_Client_Ids( $uid ),
            'add' => ['Junk', '$Junk'],
            'remove' => ['NonJunk', '$NonJunk']
        ];
        
        $this->client->store( $folderName, $options );
    }
    public function unmarkJunk($uid, $folderName) {
        $options = [
            'uids' => new \Horde_Imap_Client_Ids( $uid ),
            'add' => ['NonJunk', '$NonJunk'],
            'remove' => ['Junk', '$Junk']
        ];
        
        $this->client->store( $folderName, $options );
    }
    
    public function moveMailByUid($uid, $srcFolder, $dstFolder) {
        $opts = array();
        $opts['ids']  = new \Horde_Imap_Client_Ids( $uid );
        $opts['move'] = true;
        
        $r = $this->client->copy( $srcFolder, $dstFolder, $opts );
        
        // copy() returns array( 'old_uid' => 'new_uid' )
        if (isset($r[$uid]) && $r[$uid]) {
            return $r[$uid];
        }
        else {
            return false;
        }
    }
    
    public function moveMail($mail, $sourceFolder, $targetFolder) {
        $uids = $this->lookupUid( $sourceFolder, $mail );
        
        if (count($uids) == 0) {
            // TODO: not found
            return false;
        }
        
        $uid = $uids[0];
        
        $opts = array();
        $opts['ids']  = new \Horde_Imap_Client_Ids( $uid );
        $opts['move'] = true;
        
        $r = $this->client->copy( $sourceFolder, $targetFolder, $opts );
        
        // copy() returns array( 'old_uid' => 'new_uid' )
        if (isset($r[$uid]) && $r[$uid]) {
            return $r[$uid];
        }
        else {
            return false;
        }
    }
    
    
    
    /**
     * poll() - checks if there's new mail
     *
     * @return boolean true/false, true if there's new mail
     */
    public function poll() {
        // no connection? => try to connect
        if ($this->isConnected() == false) {
            if (!$this->connect()) {
                return false;
            }
        }
        
        // fetch mailbox status
        $oldCheck = $this->check;
        try {
            $this->check = $this->client->status( 'INBOX', \Horde_Imap_Client::STATUS_FORCE_REFRESH | \Horde_Imap_Client::STATUS_MESSAGES );
        } catch (\Exception $ex) {
            try {
                // disconnect after error
                $this->disconnect();
            } catch (\Exception $ex) { }
            
            print_cli_info( 'HordeConnector::poll, error: '.$ex->getMessage() ); 
            return false;
        }
        
        
        $checkMailbox = false;
        
        // first run & check-succeeded? => return true
        if ($oldCheck == null && isset($this->check['messages']) && $this->check['messages']) {
            $checkMailbox = true;
        }
        // ..nd-run? => compare with previous response
        if (is_array($oldCheck) && is_array($this->check) && $oldCheck['messages'] != $this->check['messages']) {
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
    
    
    
    public function lookupUid($folderName, $mail) {
        $mail->parseMail();
        
        
        if ($mail->getParsedMail() == null) {
            return array();
        }

        $q = new \Horde_Imap_Client_Search_Query();
        
        $messageid = $mail->getParsedMail()->getHeader('message-id');
        if ($messageid) {
            $q->headerText('message-id', $messageid);
        }
        else {
            $date = $mail->getParsedMail()->getHeader('date');
            if ($date)
                $q->headerText('date', $date);
                
            $subject = $mail->getParsedMail()->getHeader('subject');
            if ($subject)
                $q->headerText('subject', $subject);
            
            $from = $mail->getParsedMail()->getHeader('from');
            if ($from)
                $q->headerText('from', $from);
        }
        
            
            
        $opts = array();
        $opts['sort'] = array(\Horde_Imap_Client::SORT_ARRIVAL);//, \Horde_Imap_Client::SORT_REVERSE);
        $uids = $this->client->search( $folderName, $q, $opts );
        
        if (isset($uids['match'])) {
            $uids = $uids['match']->ids;
            
            // sometimes mails are received duplicate. max it out..
            if (count($uids) < 5) {
                return $uids;
            }
            else {
                // TODO: report for debugging
            }
        }
        
        return array();
    }
    
    
    
    public function import() {
        $this->importInbox( );
    }
   
    
    public function unsetMailFlags($mail, $folderName, $removeFlags) {
        $uids = $this->lookupUid( $folderName, $mail );
   
        if (is_array($removeFlags) == false)
            $flags = array( $removeFlags );
        
        for($x=0; $x < count($flags); $x++) {
            $f = $flags[$x];
            if (strpos($f, '\\') === false)
                $f = '\\'.$f;
        }
        
        $options = [
            'uids' => new \Horde_Imap_Client_Ids( $uids ),
            'remove' => $removeFlags//['NonJunk', '$NonJunk']
        ];
        
        $this->client->store( $folderName, $options );
    }
    public function setMailFlags($mail, $folderName, $flags) {
        $uids = $this->lookupUid( $folderName, $mail );
        
        if (is_array($flags) == false)
            $flags = array( $flags );
        
        $removeFlags = array();
        for($x=0; $x < count($flags); $x++) {
            $f = $flags[$x];
            if (strpos($f, '\\') === false)
                $f = '\\'.$f;
            
            // toggle junk
            if (strtolower($f) == '\\junk') {
                $removeFlags[] = '\\NonJunk';
                $removeFlags[] = '\\$NonJunk';
            }
            else if (strtolower($f) == '\\nonjunk') {
                $removeFlags[] = '\\Junk';
                $removeFlags[] = '\\$Junk';
            }
            
            else if (strtolower($f) == '\\seen') {
                $removeFlags[] = '\\UnSeen';
            }
            else if (strtolower($f) == '\\unseen') {
                $removeFlags[] = '\\Seen';
            }
        }
        
        
        $options = [
            'uids' => new \Horde_Imap_Client_Ids( $uids ),
            'add' => $flags,//['Junk', '$Junk'],
            'remove' => $removeFlags//['NonJunk', '$NonJunk']
        ];
        
        $this->client->store( $folderName, $options );
    }
    
    
    public function deleteMail($mail) {
        $folder = $mail->getMailboxName();
        
        $uids = $this->lookupUid($folder, $mail);
        
        if (count($uids) == 0) {
            return false;
        }
        
        return $this->deleteMailByUid( $folder, $uids[0] );
    }
    
    public function deleteMailByUid($folder, $uids) {
        if (is_array($uids) == false) {
            $uids = array($uids);
        }
        
        $list = new \Horde_Imap_Client_Ids($uids);
        $this->client->expunge($folder, array('ids' => $list, 'delete' => true));
    }
    
    public function appendMessage($mailbox, $message, $options=null, $internal_date=null) {
        if (trim($message) == '') {
            throw new InvalidStateException( 'Empty message' );
        }
        
        
        $flags = array();
        $flags[] = \Horde_Imap_Client::FLAG_SEEN;
        
        $data = array();
        $data[] = array('data' => $message
            , 'flags' => $flags
        );
        
        $this->client->append( $mailbox, $data );
    }
    
    public function emptyFolder($folderName) {
        // search all uid's
        $q_all = new \Horde_Imap_Client_Search_Query();
        $r = $this->client->search( $folderName, $q_all );
        
        // loop-delete
        $ids = $r['match']->ids;
        $count=0;
        if (count($ids) > 0) {
            do {
                $idsToDelete = array_splice($ids, 0, 50);
                $this->deleteMailByUid( $folderName, $idsToDelete);
                
                $count += count( $idsToDelete );
            } while (count($ids) > 0);
        }
        
        return $count;
    }
    
    
}


