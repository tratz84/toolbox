<?php

namespace webmail\mail;


use core\ObjectContainer;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrMail;
use core\exception\NotForLiveException;

class ImapConnection {
    
    protected $ctx;
    
    protected $hostname;
    protected $port;
    protected $username;
    protected $password;
    
    protected $connector = null;
    
    protected $connectionOptions = array();
    
    protected $mailbox = null;
    protected $imap = null;
    
    protected $errors = null;
    
    protected $callback_itemImported = null;
    
    protected $serverPropertyChecksums = null;
    
    protected $imapFetchListCount = -1;
    protected $imapFetchOverviewOptions = 0;
    
    // just import messages 'SINCE'
    protected $sinceUpdate = null;
    
    
    public function __construct($hostname=null, $port=null, $username=null, $password=null) {
        
        $this->ctx = \core\Context::getInstance();
        
        $this->setHostname($hostname);
        $this->setPort($port);
        $this->setUsername($username);
        $this->setPassword($password);
        
        $this->connectionOptions[] = 'imap';
        $this->connectionOptions[] = 'novalidate-cert';
    }
    
    public static function createByConnector(Connector $c) {
        $ic = new self();
        
        $ic->setHostname($c->getHostname());
        $ic->setPort($c->getPort());
        $ic->setUsername($c->getUsername());
        $ic->setPassword($c->getPassword());
        
        $ic->setConnector($c);
        
        return $ic;
    }
    
    
    public function setHostname($p) { $this->hostname = $p; }
    public function getHostname( ) { return $this->hostname; }
    
    public function setPort($p) { $this->port = $p; }
    public function getPort( ) { return $this->port; }
    
    public function setUsername($p) { $this->username = $p; }
    public function getUsername( ) { return $this->username; }
    
    public function setPassword($p) { $this->password = $p; }
    public function getPassword( ) { return $this->password; }
    
    public function setConnector($c) { $this->connector = $c; }
    public function getConnector() { return $this->connector; }
    
    public function getErrors() { return $this->errors; }
    
    public function setSinceUpdate($t) { $this->sinceUpdate = $t; }
    
    public function setCallbackItemImported($callback) {
        $this->callback_itemImported = $callback;
    }
    
    
    
    public function connect() {
        if ($this->getPort() == 993) {
            $this->connectionOptions[] = 'ssl';
        }
        
        
        $strOpts = implode('/', $this->connectionOptions);
        $strOpts = '/' . $strOpts;
        
        $this->mailbox = '{'.$this->getHostname().':'.$this->getPort().$strOpts.'}';
        
        $this->imap = @\imap_open($this->mailbox, $this->getUsername(), $this->getPassword());
        
        if ($this->imap === false) {
            $this->errors = imap_errors();
            
            return false;
        }
        
        return true;
    }
    
    public function isConnected() {
        if ($this->imap !== false && $this->imap !== null) {
            return true;
        } else {
            return false;
        }
    }
    
    public function disconnect() {
        if ($this->imap === null) return;
        
        imap_close( $this->imap );
        $this->imap = null;
    }
    
    
    public function listFolders() {
        $folders = imap_listmailbox($this->imap, $this->mailbox, "*");
        
        foreach($folders as &$f) {
            $f = str_replace($this->mailbox, '', $f);
        }
        
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
    
    public function ping() {
        if ($this->imap === null) return false;
        
        return imap_ping($this->imap);
    }
    
    public function check() {
        return imap_check($this->imap);
    }

    
    public function importItems($folderName) {
        
        if (!imap_reopen($this->imap, $this->mailbox.$folderName))
            return false;
        
        $messageCount = imap_check($this->imap);
        
        $items = array();
        
        // Fetch an overview for all messages in INBOX
        $overviewList = $this->buildOverviewList( $messageCount );

        // fetch messages
        $x=0;
        foreach($overviewList as $range) {
            $results = imap_fetch_overview($this->imap, $range, $this->imapFetchOverviewOptions);
            
            if (is_cli()) {
                print "Importing msg: " . $folderName . " (" . $x . "/" . $this->imapFetchListCount . ')'."\n";
            }
            
            for($y=0; $y < count($results); $y++) {
                $emlfile = $this->determineEmailPath( $results[$y] );
                
                // INBOX has special business rules. Skip import if e-mail is not yet imported by bin/webmail_connector.php
                if ($folderName == 'INBOX' && file_exists($emlfile) == false) {
                    continue;
                }
                
                $mp = $this->buildMessageProperties($emlfile, $folderName, $results[$y]);
                
                // check if mail (properties) are changed
                $changed = $this->serverPropertiesChanged($emlfile, $mp);
                
                if ($changed) {
                    $mp->save();
                    
                    // TODO: if Folder = Sent, check 'In-Reply-To'-header & lookup replied e-mail. If status == 'open', set to REPLIED
                    $this->saveMessage($folderName, $results[$y]);
                }
                
                // callback (probably Solr-import)
                call_user_func($this->callback_itemImported, $folderName, $results[$y], $emlfile, $changed);
            }
            
            imap_gc($this->imap, IMAP_GC_ELT | IMAP_GC_ENV | IMAP_GC_TEXTS);
            
            $x += count($results);
        }
        
        return $items;
    }
    
    protected function determineEmailPath($overview) {
        $dt = new \DateTime();
        $dt->setTimestamp($overview->udate);
        $dt->setTimezone(new \DateTimeZone('+0000'));
        
        $uid = @md5($overview->size . $overview->message_id . $overview->from . $overview->subject . $overview->udate);
        
        $p = $this->ctx->getDataDir() . '/webmail/inbox/' . $dt->format('Y') . '/' . $dt->format('m') . '/' . $dt->format('d');
        
        $file = $p . '/' . $uid . '.eml';
        
        return $file;
    }
    
    
    public function buildMessageProperties($emlFile, $folderName, $overview) {
        // $mp = message-properties
        $mp = new MailProperties($emlFile);
        $mp->setServerProperty('connectorId',          $this->connector->getConnectorId());
        $mp->setServerProperty('connectorDescription', $this->connector->getDescription());
        
        $mp->setServerProperty('folder',      $folderName);
        $mp->setServerProperty('subject',     @$overview->subject);
        $mp->setServerProperty('from',        @$overview->from);
        $mp->setServerProperty('to',          @$overview->to);
        $mp->setServerProperty('size',        @$overview->size);
        $mp->setServerProperty('message_id',  @$overview->message_id);
        $mp->setServerProperty('uid',         @$overview->uid);
        $mp->setServerProperty('udate',       @$overview->udate);
        $mp->setServerProperty('flagged',     @$overview->flagged);
        $mp->setServerProperty('answered',    @$overview->answered);
        $mp->setServerProperty('deleted',     @$overview->deleted);
        $mp->setServerProperty('seen',        @$overview->seen);
        $mp->setServerProperty('draft',       @$overview->draft);
        
        if ($mp->toolboxPropertyFileExists() == false && $mp->getProperty('action') == '') {
            if (@$overview->answered && ($mp->getAction() == '' || $mp->getAction() == 'open')) {
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
    
    /**
     * returns filename if new , else true/false
     */
    protected function saveMessage($folderName, $overview) {
        $file = $this->determineEmailPath($overview);
        
        if (is_dir(dirname($file)) == false) {
            if (mkdir(dirname($file), 0755, true) == false) {
                return false;
            }
        }
        
        $mp = $this->buildMessageProperties($file, $folderName, $overview);
        
        // TODO: props changed..?
        $mp->save();
        
        // mail/eml itself won't ever change. Only it's overhead (that's put into the .properties-file)
        if (file_exists($file)) {
            return false;
        }
        
        $str = imap_fetchheader($this->imap, $overview->uid, FT_UID);
        $str .= imap_body($this->imap, $overview->uid, FT_PEEK | FT_UID);
        
        if (is_cli()) {
            print "Saving e-mail to file: $file\n";
        }
        
        $fh = fopen($file, 'w');
        if (!$fh) {
            print "ERROR: Unable to open file: $file\n"; 
            return false;
        }
        
        $r = fwrite($fh, $str);
        if ($r != strlen($str)) {
            // TODO: handle this?  skip? disk full?
        }
        
        fclose($fh);
        
        imap_gc($this->imap, IMAP_GC_ELT | IMAP_GC_ENV | IMAP_GC_TEXTS);
        
        return $file;
    }
    
    public function serverPropertiesChanged($filename, MailProperties $data) {
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
        
        if (isset($this->serverPropertyChecksums[ $filename ]) && $this->serverPropertyChecksums[ $filename ] == $chksum) {
            return false;
        }
        
        $this->serverPropertyChecksums[ $filename ] = $chksum;
        
        return true;
    }
    
    public function saveServerPropertyChecksums() {
        
        return save_data('webmail/message-checksums', serialize($this->serverPropertyChecksums));
    }
    
    
    protected function buildOverviewList( $check ) {
        $list = array();
        
        if ($this->sinceUpdate) {
            $uids = $this->search(null, [ ['key' => 'SINCE', 'value' => $this->sinceUpdate] ]);
            $chunked_uids = $uids ? array_chunk($uids, 100) : array();
            
            foreach($chunked_uids as $cuid) {
                $list[] = implode(',', $cuid);
            }
            
            $this->imapFetchListCount = $uids ? count($uids) : 0;
            $this->imapFetchOverviewOptions = FT_UID;
        } else {
            $this->imapFetchOverviewOptions = 0;
            
            $pagesize = 500;
            
            // build list
            for($x=1; $x <= $check->Nmsgs; $x += $pagesize) {
                $end = ($x + $pagesize) - 1;
                
                if ($end > $check->Nmsgs)
                    $end = $check->Nmsgs;
                   
                $list[] = $x . ':' . $end;
            }
            
            $this->imapFetchListCount = $check->Nmsgs;
        }
        
        return $list;
    }
    
    public function importInbox(Connector $connector) {
        if (!imap_reopen($this->imap, $this->mailbox.'INBOX')) {
            return false;
        }
        
        $items = array();
        
        $messageCount = imap_check($this->imap);
        
        $blnExpunge = false;
        
        // Fetch an overview for all messages in INBOX
        $listOverview = $this->buildOverviewList( $messageCount );
        foreach($listOverview as $range) {
            $results = imap_fetch_overview($this->imap, $range, $this->imapFetchOverviewOptions);
            
            for($y=0; $y < count($results); $y++) {
                if ($results[$y]->deleted)
                    continue;
                
                // save file locally
                $file = $this->determineEmailPath( $results[$y] );
                
                if (file_exists($file) == false) {
                    $emlfile = $this->saveMessage('INBOX', $results[$y]);
                    
                    // new?
                    if ($emlfile) {
                        // apply filters
                        print "Applying filters\n";
                        $result = $this->applyFilters($connector, $file, $results[$y]->uid);
                        
                        // filters applied? => expunge mailbox when finished
                        if (is_array($result)) {
                            $blnExpunge = true;
                        }
                        
                        // call callback
                        if ($this->callback_itemImported != null) {
                            // message moved to another folder?
                            if (is_array($result) && isset($result['action']) && $result['action'] == 'move_to_folder') {
                                $folderName = $result['value'];
                                
                                // update properties
                                $mp = new MailProperties($emlfile);
                                $mp->load();
                                $mp->setFolder($folderName);
                                // mark as spam
                                if (is_array($result) && $result['is_spam']) {
                                    $mp->setJunk( true );
                                }
                                $mp->save();
                            }
                            // set default folder
                            else {
                                $folderName = 'INBOX';
                            }
                            
                            call_user_func($this->callback_itemImported, $folderName, $results[$y], $file, true);
                        }
                    }
                }
            }
        }
        
        if ($blnExpunge) {
            imap_expunge($this->imap);
        }
        
        imap_gc($this->imap, IMAP_GC_ELT | IMAP_GC_ENV | IMAP_GC_TEXTS);
        
        return $items;
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
                
                if ($actions[0]->getFilterAction() == 'move_to_folder') {
                    $connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);
                    $f = $connectorService->readImapFolder( $actions[0]->getFilterActionValue() );
                    
                    // found? => move
                    if ($f) {
                        if ($isSpam) {
                            imap_setflag_full($this->imap, $messageUid, 'Junk', ST_UID);
                            imap_setflag_full($this->imap, $messageUid, '$Junk', ST_UID);
                        }
                        
                        if (imap_mail_move($this->imap, $messageUid, $f->getFolderName(), CP_UID)) {
                            
                        }
                        
                        return array('action' => 'move_to_folder', 'value' => $f->getFolderName(), 'is_spam' => $isSpam);
                    }
                }
                
                return null;
            }
        }
        
        return null;
    }

    
    public function deleteMailByUid($folder, $uid) {
        if (!imap_reopen($this->imap, $this->mailbox.$folder)) {
            return false;
        }
        
        return imap_delete($this->imap, $uid, FT_UID);
    }
    
    
    public function moveMailByUid($uid, $sourceFolder, $targetFolder) {
        if (!imap_reopen($this->imap, $this->mailbox.$sourceFolder)) {
            return false;
        }
        
        return imap_mail_move($this->imap, $uid, $targetFolder, CP_UID);
    }
    
    public function setFlagByUid($uid, $folder, $flags) {
        if (!imap_reopen($this->imap, $this->mailbox.$folder)) {
            return false;
        }
        
        return imap_setflag_full($this->imap, $uid, $flags, ST_UID);
    }
    public function clearFlagByUid($uid, $folder, $flags) {
        if (!imap_reopen($this->imap, $this->mailbox.$folder)) {
            return false;
        }
        
        return imap_clearflag_full($this->imap, $uid, $flags, ST_UID);
    }
    
    public function imapAppend($mailbox, $message, $options=null, $internal_date=null) {
        return imap_append($this->imap, $this->mailbox.$mailbox, $message, "\\Seen");
    }
    
    public function deleteFolder($folderName) {
        if (!imap_reopen($this->imap, $this->mailbox.$folderName)) {
            return false;
        }
        
        $c = imap_check($this->imap);
        
        return imap_delete($this->imap, '1:'.$c->Nmsgs);
    }
    
    
    public function expunge() {
        return imap_expunge($this->imap);
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
            
            if (is_cli())
                print "Importing: " . $if->getFolderName() . "\n";
            
            $this->importItems( $if->getFolderName() );
        }
    }
    
    public function search($folder, $criteria=array()) {
        // use current selected folder if $folder==null
        if ($folder != null) {
            if (imap_reopen($this->imap, $this->mailbox.$folder) == false) {
                return false;
            }
        }
        
        // build search criteria
        $str = '';
        foreach($criteria as $crit) {
            $key = $crit['key'];
            
            if ($str != '')
                $str = $str . ' ';
            
            
            if (in_array($key, ['BCC', 'BEFORE', 'BODY', 'CC', 'FROM', 'KEYWORD', 'ON', 'SINCE', 'SUBJECT', 'TEXT', 'TO', 'UNKEYWORD'])) {
                $str .= $key . ' "' . addslashes($crit['value']) . '"';
            }
            else if (in_array($key, ['ALL', 'ANSWERED', 'DELETED', 'FLAGGED', 'NEW', 'OLD', 'RECENT', 'SEEN', 'UNANSWERED', 'UNDELETED', 'UNFLAGGED', 'UNSEEN'])) {
                $str .= $key;
            }
            else {
                throw new \core\exception\InvalidArgumentException('Invalid search keyword: ' . $key);
            }
        }
        
        return imap_search($this->imap, $str, SE_UID, 'UTF-8');
    }
    
    public function lookupUid($folder, SolrMail $solrMail) {
        $solrMail->parseMail();
        
        if ($solrMail->getParsedMail() == null) {
            return array();
        }
        
        $uids = $this->search($folder, [
              [ 'key' => 'ON',      'value' => $solrMail->getParsedMail()->getHeader('date') ]
            , [ 'key' => 'SUBJECT', 'value' => $solrMail->getSubject()]
            , [ 'key' => 'FROM',    'value' => $solrMail->getFromEmail()]
        ]);
        
        
        return $uids;
    }
    
    
    
}



