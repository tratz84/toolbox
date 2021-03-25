<?php


namespace webmail\mail\connector;

use core\ObjectContainer;
use Horde_Imap_Client_Socket;
use webmail\mail\MailProperties;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrMail;
use core\exception\NotImplementedException;


class HordeConnector extends BaseMailConnector {
    
    protected $connectionOptions = array();
    
    /**
     * @var \Horde_Imap_Client_Socket
     */
    protected $client = null;
    
    protected $errors = null;
    
    protected $callback_itemImported = null;
    
    protected $serverPropertyChecksums = null;
    
    protected $imapFetchListCount = -1;
    protected $imapFetchOverviewOptions = 0;
    
    // just import messages 'SINCE'
    protected $sinceUpdate = null;
    
    protected $messageCountInbox = null;
    
    
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
            
            $this->client = new \Horde_Imap_Client_Socket( $opts );
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
        $fetchQuery->size();
        $fetchQuery->uid();
        $fetchQuery->envelope();
        $fetchQuery->size();
        $fetchQuery->flags();
        
        
        
        $list = $this->client->fetch( $folderName, $fetchQuery, array(
            'ids' => new \Horde_Imap_Client_Ids( $results['match'] )
        ));
        
        
        $ids = $list->ids();
        $x=0;
        foreach($ids as $id) {
            $x++;
            
            // cli? => debug info
            if (is_cli()) {
                print_info("Importing msg: " . $folderName . " (" . $x . "/" . count($ids) . ')');
            }
            
            // ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Fetch.html
            $cdf = $list->offsetGet( $id );
            
            // ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Envelope.html
//             $env = $cdf->getEnvelope();
            
            $emlfile = $this->determineEmailPath( $cdf );
            
            if ($folderName == 'INBOX' && file_exists($emlfile) == false) {
                continue;
            }
            
            $mp = $this->buildMessageProperties($emlfile, $folderName, $cdf);
            
            // check if mail (properties) are changed
            $changed = $this->serverPropertiesChanged($emlfile, $mp);
            
            if ($changed) {
                $mp->save();
                
                // TODO: if Folder = Sent, check 'In-Reply-To'-header & lookup replied e-mail. If status == 'open', set to REPLIED
                $this->saveMessage($folderName, $cdf);
            }
            
            // callback (probably Solr-import)
            call_user_func($this->callback_itemImported, $folderName, $results[$y], $emlfile, $changed);

        }
    }
    
    public function buildMessageProperties($emlFile, $folderName, \Horde_Imap_Client_Data_Fetch $cdf) {
        // $cdf - ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Fetch.html
        
        // ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Envelope.html
        $env = $cdf->getEnvelope();
        
        
        // $mp = message-properties
        $mp = new MailProperties($emlFile);
        $mp->setServerProperty('connectorId',          $this->connector->getConnectorId());
        $mp->setServerProperty('connectorDescription', $this->connector->getDescription());
        
        $mp->setServerProperty('folder',      $folderName);
        $mp->setServerProperty('subject',     @$env->subject);
        $mp->setServerProperty('from',        @$env->from->writeAddress());
        $mp->setServerProperty('to',          @$env->to->writeAddress());
        $mp->setServerProperty('size',        @$cdf->getSize());
        $mp->setServerProperty('message_id',  @$env->message_id);
        $mp->setServerProperty('uid',         @$cdf->getUid());
        $mp->setServerProperty('udate',       (int)@$cdf->getImapDate()->format('U'));
        
        
        $flags = $cdf->getFlags();
        
        $mp->setServerProperty('flagged',     in_array('\\flagged', $flags) ? 1 : 0);
        $mp->setServerProperty('answered',    in_array('\\answered', $flags) ? 1 : 0);
        $mp->setServerProperty('deleted',     in_array('\\deleted', $flags) ? 1 : 0);
        $mp->setServerProperty('seen',        in_array('\\seen', $flags) ? 1 : 0);
        $mp->setServerProperty('draft',       in_array('\\draft', $flags) ? 1 : 0);
        
        if ($mp->toolboxPropertyFileExists() == false && $mp->getProperty('action') == '') {
            if (@$mp->getProperty('answered') && ($mp->getAction() == '' || $mp->getAction() == 'open')) {
                // maybe also do this for ACTION_URGENT ?
                $mp->setAction(SolrMail::ACTION_REPLIED);
            // TODO: shouldn't be hard-coded
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
    protected function saveMessage($folderName, \Horde_Imap_Client_Data_Fetch $cdf) {
        $file = $this->determineEmailPath($cdf);
        
        if (is_dir(dirname($file)) == false) {
            if (mkdir(dirname($file), 0755, true) == false) {
                return false;
            }
        }
        
        $mp = $this->buildMessageProperties($file, $folderName, $cdf);
        
        // TODO: props changed..?
        $mp->save();
        
        // mail/eml itself won't ever change. Only it's overhead (that's put into the .properties-file)
        if (file_exists($file)) {
            return false;
        }
        
        $str = $this->fetchRawMail($folderName, $cdf->getUid());
        
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
    
    
    protected function fetchRawMail($folderName, $id) {
        // create query
        $fq2 = new \Horde_Imap_Client_Fetch_Query();
        $fq2->headerText();
        $fq2->bodyText();
        
        // fetch
        $list2 = $this->client->fetch( $folderName, $fq2, array(
            'ids' => new \Horde_Imap_Client_Ids( array($id) )
        ));
        
        // 1st item
        $cdf2 = $list2->first();
        
        // 
        return $cdf2->getHeaderText() . $cdf2->getBodyText();
    }
    
    
    
    
    public function importInbox(Connector $connector) {
        if (!imap_reopen($this->imap, imap_utf7_encode($this->mailbox.'INBOX'))) {
            return false;
        }
        
        $items = array();
        
        $searchQuery = new \Horde_Imap_Client_Search_Query();
//         $searchQuery->dateSearch(new \DateTime('2021-03-01 00:00:00'), \Horde_Imap_Client_Search_Query::DATE_SINCE);
        
        $results = $this->client->search( 'INBOX', $searchQuery );
        
        $fetchQuery = new \Horde_Imap_Client_Fetch_Query();
        $fetchQuery->imapDate();
        $fetchQuery->size();
        $fetchQuery->uid();
        $fetchQuery->envelope();
        $fetchQuery->size();
        $fetchQuery->flags();
        
        $list = $this->client->fetch( 'INBOX', $fetchQuery, array(
            'ids' => new \Horde_Imap_Client_Ids( $results['match'] )
        ));
        
        
        $ids = $list->ids();
        $x=0;
        
        $blnExpunge = false;
        
        // Fetch an overview for all messages in INBOX
        $ids = $list->ids();
        $x=0;
        foreach($ids as $id) {
            $x++;
            
            // ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Fetch.html
            /**
             * @var \Horde_Imap_Client_Data_Fetch $cdf
             */
            $cdf = $list->offsetGet( $id );
            
            // ref @ https://dev.horde.org/api/master/lib/Imap_Client/classes/Horde_Imap_Client_Data_Envelope.html
            $env = $cdf->getEnvelope();
            
            // skip if marked as deleted
            if (in_array('\\deleted', $cdf->getFlags()))
                continue;
            
            // save file locally
            $file = $this->determineEmailPath( $cdf );
            
            if (file_exists($file)) {
                continue;
            }
            
            $emlfile = $this->saveMessage('INBOX', $cdf);
            
            // save failed? happends when it's already saved..
            if (!$emlfile) {
                continue;
            }
            
            // apply filters
            print_info("Applying filters");
            $result = $this->applyFilters($connector, $file, $cdf);
            
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
            if ($this->callback_itemImported != null) {
                call_user_func($this->callback_itemImported, $folderName, $results[$y], $file, true);
            }
        }
        
        if ($blnExpunge) {
            $this->client->expunge( 'INBOX' );
        }
        
        return $items;
    }
    
    protected function applyFilters($connector, $file, \Horde_Imap_Client_Data_Fetch $cdf) {
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
                    
                $return_value = array();
                $return_value['is_spam'] = $isSpam;
                
                $moveFolderActionValue = null;
                foreach($actions as $action) {
                    if ($action->getFilterAction() == 'move_to_folder') {
                        $moveFolderActionValue = $action->getFilterActionValue();               // this is an webmail__connector_imapfolder.connector_imapfolder_id
                    }
                    if ($action->getFilterAction() == 'set_action') {
                        $return_value['set_action'] = $action->getFilterActionValue();
                    }
                }
                
                if ($moveFolderActionValue) {
                    $connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);
                    $f = $connectorService->readImapFolder( $moveFolderActionValue );
                    
                    // found? => move
                    if ($f) {
                        if ($isSpam) {
                            $flags = $cdf->getFlags();
                            if (in_array("\\junk", $flags) == false)
                                $flags[] = "\\junk";
                            if (in_array("\\\$junk", $flags) == false)      // '\$junk'
                                $flags[] = "\\\$junk";
                            
                            $cdf->setFlags( $flags );
                        }
                        
                        $this->moveMailByUid( $cdf->getUid(), 'INBOX', $f->getFolderName() );
                        
                        $return_value['move_to_folder'] = $f->getFolderName();
                    }
                }
                
                return $return_value;
            }
        }
        
        return array();
    }
    
    
    public function deleteMailByUid($folder, $uid) {
        $this->client->store( $folder, array('add' => array(\Horde_Imap_Client::FLAG_DELETED), 'ids' => new \Horde_Imap_Client_Ids($uid)));
        
        // TODO: expunge..
        
        return true;
    }
    
    
    public function moveMailByUid($uid, $sourceFolder, $targetFolder) {
        $this->client->copy( $sourceFolder, $targetFolder, array('ids' => new \Horde_Imap_Client_Ids( $cdf->getUid() )) );
        
        $this->client->store( $sourceFolder, array('add' => array(\Horde_Imap_Client::FLAG_DELETED), 'ids' => new \Horde_Imap_Client_Ids($uid)));
        
        // TODO: expunge..
        
        return true;
    }
    
    public function markMail($uid, $folder, $flags) {
        if (!imap_reopen($this->imap, imap_utf7_encode($this->mailbox.$folder))) {
            return false;
        }
        
        return imap_setflag_full($this->imap, $uid, $flags, ST_UID);
    }
    
    public function markJunk($uid, $folder) {
        $this->client->store( $folder, array('add' => array('$junk', 'junk'), 'ids' => new \Horde_Imap_Client_Ids($uid)));
        $this->client->store( $folder, array('remove' => array('$nonjunk', 'nonjunk'), 'ids' => new \Horde_Imap_Client_Ids($uid)));
    }
    
    public function clearFlagByUid($uid, $folder, $flags) {
        $this->client->store( $folder, array('remove' => $flags, 'ids' => new \Horde_Imap_Client_Ids($uid)));
    }
    
    public function appendMessage($mailbox, $message, $options=null, $internal_date=null) {
        $this->client->append( $mailbox, $message, ['\\seen'], $internal_date);
    }
    
    public function emptyFolder($folderName) {
        $searchQuery = new \Horde_Imap_Client_Search_Query();
        
        $results = $this->client->search( $folderName, $searchQuery );
        
        $this->client->store( $folderName, array('add' => array(\Horde_Imap_Client::FLAG_DELETED), 'ids' => new \Horde_Imap_Client_Ids($results['match'])));
    }
    
    
    public function expunge( ) {
        throw new NotImplementedException('not yet implemented');
//         return $this->client->expunge( $mailbox );
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
                print_info("Importing: " . $if->getFolderName());
                
            $this->importItems( $if->getFolderName() );
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
        

        // fetch message count INBOX
        $searchQuery = new \Horde_Imap_Client_Search_Query();
        $results = $this->client->search( 'INBOX', $searchQuery );
        
        
        $prevCount = $this->messageCountInbox;
        $this->messageCountInbox = $results['count'];
        
        
        if ($this->messageCountInbox != $prevCount) {
            return true;
        }
        else {
            return false;
        }
    }
    
    
    
    
    
}


