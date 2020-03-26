<?php

namespace webmail\mail;


use webmail\model\Connector;
use core\ObjectContainer;
use webmail\service\ConnectorService;

class ImapConnection {
    
    protected $ctx;
    
    protected $hostname;
    protected $port;
    protected $username;
    protected $password;
    
    protected $connectionOptions = array();
    
    protected $mailbox = null;
    protected $imap = null;
    
    protected $errors = null;
    
    protected $callback_itemImported = null;
    
    protected $messagePropertyChecksums = null;
    
    
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
    
    
    public function getErrors() { return $this->errors; }
    
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
    
    public function listItems($folderName) {
        if (!imap_reopen($this->imap, $this->mailbox.$folderName))
            return false;
        
        $messageCount = imap_check($this->imap);
        
        $items = array();
        
        // Fetch an overview for all messages in INBOX
        $pagesize = 50;
        for($x=1; $x < $messageCount->Nmsgs; $x += $pagesize) {
            $end = ($x + $pagesize) - 1;
            if ($end > $messageCount->Nmsgs)
                $end = $messageCount->Nmsgs;
            
            $results = imap_fetch_overview($this->imap, $x.':'.$end, 0);
            
            foreach ($results as $o) {
                $items[] = $o;
            }
        }
        
        imap_gc($this->imap, IMAP_GC_ELT | IMAP_GC_ENV | IMAP_GC_TEXTS);
        
        return $items;
    }
    
    
    public function importItems($folderName) {
        if (!imap_reopen($this->imap, $this->mailbox.$folderName))
            return false;
            
        $messageCount = imap_check($this->imap);
        
        $items = array();
        
        // Fetch an overview for all messages in INBOX
        $pagesize = 500;
        for($x=1; $x <= $messageCount->Nmsgs; $x += $pagesize) {
            $end = ($x + $pagesize) - 1;
            
            if ($end > $messageCount->Nmsgs)
                $end = $messageCount->Nmsgs;
                
            $results = imap_fetch_overview($this->imap, $x.':'.$end, 0);
            
            if (is_cli()) {
                print "Importing msg: " . $folderName . " (" . $x . "/" . $messageCount->Nmsgs . ')'."\n";
            }
            
            for($y=0; $y < count($results); $y++) {
                $mp = $this->buildMessageProperties($folderName, $results[$y]);
                $emlfile = $this->determineEmailPath( $results[$y] );
                
                // check if mail (properties) are changed
                $data_mp = json_encode($mp);
//                 print $data_mp . "\n";
//                 print "$emlfile\n";
                $changed = $this->messagePropertiesChanged($emlfile.'.properties', $data_mp);
                if ($changed) {
                    $this->saveMessage($folderName, $results[$y], $x+$y);
                    
                }
                
                // callback (probably Solr-import)
                call_user_func($this->callback_itemImported, $folderName, $results[$y], $emlfile, $changed);
            }
            
            imap_gc($this->imap, IMAP_GC_ELT | IMAP_GC_ENV | IMAP_GC_TEXTS);
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
    
    
    public function buildMessageProperties($folderName, $overview) {
        // $mp = message-properties
        $mp = array();
        $mp['folder']     = $folderName;
        $mp['subject']    = @$overview->subject;
        $mp['from']       = @$overview->from;
        $mp['to']         = @$overview->to;
        $mp['size']       = @$overview->size;
        $mp['message_id'] = @$overview->message_id;
        $mp['uid']        = @$overview->uid;
        $mp['udate']      = @$overview->udate;
        $mp['flagged']    = @$overview->flagged;
        $mp['answered']   = @$overview->answered;
        $mp['deleted']    = @$overview->deleted;
        $mp['seen']       = @$overview->seen;
        $mp['draft']      = @$overview->draft;
        
        return $mp;
    }
    
    /**
     * returns filename if new , else true/false
     */
    protected function saveMessage($folderName, $overview, $seqNo) {
        $file = $this->determineEmailPath($overview);
        
        if (is_dir(dirname($file)) == false) {
            if (mkdir(dirname($file), 0755, true) == false) {
                return false;
            }
        }
        
        $mp = $this->buildMessageProperties($folderName, $overview);
        
        // props changed?
        file_put_contents($file . '.properties', json_encode($mp));
        
        // mail/eml itself won't ever change. Only it's overhead (that's put into the .properties-file)
        if (file_exists($file)) {
            return false;
        }
        
        $str = imap_fetchheader($this->imap, $seqNo);
        $str .= imap_body($this->imap, $seqNo, FT_PEEK);
        
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
    
    public function messagePropertiesChanged($filename, $data) {
        $chksum = crc32_int32($data);
        
        if ($this->messagePropertyChecksums === null) {
            $f = get_data_file('webmail/message-checksums');
            if ($f)
                $this->messagePropertyChecksums = unserialize( file_get_contents( $f ) );
        }
        
        if (isset($this->messagePropertyChecksums[ $filename ]) && $this->messagePropertyChecksums[ $filename ] == $chksum) {
            return false;
        }
        
        $this->messagePropertyChecksums[ $filename ] = $chksum;
        
        return true;
    }
    
    public function saveMessagePropertyChecksums() {
        
        return save_data('webmail/message-checksums', serialize($this->messagePropertyChecksums));
    }
    
    
    public function importInbox(Connector $connector) {
        if (!imap_reopen($this->imap, $this->mailbox.'INBOX')) {
            return false;
        }
        
        $check = imap_check($this->imap);
        
        $items = array();
        
        $blnExpunge = false;
        
        // Fetch an overview for all messages in INBOX
        $pagesize = 1000;
        for($x=1; $x <= $check->Nmsgs; $x += $pagesize) {
            $end = ($x + $pagesize) - 1;
            
            if ($end > $check->Nmsgs)
                $end = $check->Nmsgs;
            
            $results = imap_fetch_overview($this->imap, $x.':'.$end, 0);
            
            for($y=0; $y < count($results); $y++) {
                if ($results[$y]->deleted)
                    continue;
                
                // save file locally
                $file = $this->determineEmailPath( $results[$y] );
                
                if (file_exists($file) == false) {
                    $emlfile = $this->saveMessage('INBOX', $results[$y], $x+$y);
                    
                    // new?
                    if ($emlfile) {
                        // apply filters
                        print "Applying filters\n";
                        $result = $this->applyFilters($connector, $file, $x+$y);
                        
                        // filters applied? => expunge mailbox when finished
                        if (is_array($result)) {
                            $blnExpunge = true;
                        }
                        
                        // call callback
                        if ($this->callback_itemImported != null) {
                            // message moved to another folder?
                            if (is_array($result) && isset($result['action']) && $result['action'] == 'move_to_folder') {
                                $folderName = $result['value'];
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
    
    protected function applyFilters($connector, $file, $messageNo) {
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
                            imap_setflag_full($this->imap, $messageNo, 'Junk');
                            imap_setflag_full($this->imap, $messageNo, '$Junk');
                        }
                        
                        if (imap_mail_move($this->imap, $messageNo, $f->getFolderName())) {
                            
                        }
                        
                        return array('action' => 'move_to_folder', 'value' => $f->getFolderName());
                    }
                }
                
                return null;
            }
        }
        
    }
    
    
    
    public function doImport(Connector $c) {
        foreach($c->getImapfolders() as $if) {
            if (!$if->getActive()) {
                continue;
            }
            
            if (is_cli())
                print "Importing: " . $if->getFolderName() . "\n";
            
            $this->importItems( $if->getFolderName() );
        }
    }
    
    
    
    
}



