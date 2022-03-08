<?php


namespace webmail\search;



use webmail\MailTabSettings;
use webmail\MailboxSearchSettings;
use webmail\model\ConnectorImapfolderDAO;
use core\db\DatabaseHandler;
use webmail\model\EmailDAO;
use core\forms\lists\ListResponse;
use webmail\model\EmailToDAO;
use webmail\mail\render\MysqlMailRender;

class MysqlMailSearch extends MailSearchBase {
    
    
    protected $fromEmails = array();
    protected $toEmails = array();
    
    
    
    protected $exclFromEmails   = array();
    protected $exclToEmails     = array();
    
    
    protected $exclMailboxNames = array();
    protected $exclActions      = array();
    
    protected $mapConnectorImapFolders = null;
    
    protected $searchMethod = 'AND';
    
    
    public function __construct() {
        parent::__construct();
        
        
    }
    
    
    
    public function getFolders( $opts=array() ) {
        
        $eDao = new EmailDAO();
        $list = $eDao->listFolders();
        
        if (isset($opts['filter']) && is_array($opts['filter'])) {
            $filter = $opts['filter'];
            
            $list = array_filter($list, function($folderName) use ($filter) {
                if (in_array( $folderName['name'], $filter )) {
                    return false;
                }
                else {
                    return true;
                }
            });
        }
        
        if (isset($opts['noempty']) && $opts['noempty']) {
            $list = array_filter($list, function($folderName) {
                return $folderName['value'] > 0 ? true : false;
            });
        }
        
        usort($list, function($f1, $f2) {
            if ($f1['name'] == 'INBOX') {
                return -1;
            }
            else if ($f2['name'] == 'INBOX') {
                return 1;
            }
            
            return strcmp( $f1['name'], $f2['name'] );
        });
        
        
        return $list;
    }
    

    public function searchListResponse() {
        
        if ($this->getRows() == 0) {
            return new ListResponse(0, 0, 0, array());
        }
        
        $dh = DatabaseHandler::getConnection('default');
        
        $orderBy = 'order by created desc';
        
        $e_where = array();
        $e_params = array();
        
        $to_where = array();
        $to_params = array();
        
        // $this->fromEmail
        foreach( $this->fromEmails as $fe ) {
            $e_where[] = " from_email like ? ";
            $e_params[] = $fe;
        }
        
        
        // $this->toEmail
        foreach( $this->toEmails as $te ) {
            $to_where[] = ' to_email like ? ';
            $to_params[] = $te;
        }
        
        // $this->exclFromEmail
        foreach( $this->exclFromEmails as $fe ) {
            $e_where[] = " from_email not like ? ";
            $e_params[] = $fe;
        }
        
        // $this->exclToEmail
        foreach( $this->exclToEmails as $te ) {
            $to_where[] = ' to_email not like ? ';
            $to_params[] = $te;
        }
        
        // $this->exclMailboxName
        foreach($this->exclMailboxNames as $e) {
            $e_where[] = ' e.folderName <> ? ';
            $e_params[] = $e;
        }
        
        // $this->exclAction
        foreach( $this->exclActions as $a ) {
            $e_where[] = 'action <> ?';
            $e_params[] = $a;
        }
        
        // $this->getFolderName()
        $sub_where = array();
        foreach($this->mailboxNames as $n) {
            $sub_where[] = " e.folderName = '".$dh->escape($n)."' ";
        }
        if (count($sub_where) > 0)
            $e_where[] = ' (' . implode(') OR (', $sub_where) . ') ';
        
        // $this->getAction()
        $sub_where = array();
        foreach($this->actions as $a) {
            $sub_where[] = 'action = ?';
            $e_params[] = $a;
        }
        if (count($sub_where) > 0)
            $e_where[] = '(' . implode(') OR (', $sub_where) . ') ';
        
        
        // $this->getQuery
        $q = trim($this->getQuery());
        if ($q && $q != '*:*') {
            $q = DatabaseHandler::getConnection('default')->escape( $q );
            
            $orderByFields = array();
            
            $q = $this->parseSearchString( $q );
            
            $p = ' match(text_search) against ( \''.$q.'\' IN BOOLEAN MODE) ';
            $orderByFields[] = $p . ' desc';
            $e_where[] = $p;
            
            
//             print $p;exit;
            
            $orderBy = ' order by ' . implode(', ', $orderByFields);
        }
        else {
            $orderBy = ' order by created desc';
        }
        
        // $this->getStart()
        $limit = '';
        if ($this->getStart()) {
            $limit = 'limit ' . intval( $this->getStart() );
        }
        // $this->getRows()
        if ($this->getRows()) {
            if ($limit == '')
                $limit = 'limit 0';
            
            $limit .= ', '.intval($this->getRows());
        }
        
        
        $select_fields = "select e.*, e.created date, cif.folderName mailbox_name ";
        $sql = " from webmail__email e
                left join webmail__connector_imapfolder cif on (cif.connector_imapfolder_id = e.connector_imapfolder_id)
                where " . ($this->searchMethod == 'AND' ? '1=1' : '0=1');
        if (count($e_where)) {
            $sql .= ' '.$this->searchMethod.' ('.implode(') '.$this->searchMethod.' (', $e_where). ') ';
        }
        if (count($to_where)) {
            $sql .= ' '.$this->searchMethod.' email_id IN (select email_id from webmail__email_to where ('.implode(') '.$this->searchMethod.' (', $to_where).')  )';
        }
//         if ($orderBy)
//             $sql .= " $orderBy ";
//         $sql .= $limit;
        
        $params = array_merge( $e_params, $to_params );
//         var_export($params);
//         print "$select_fields $sql $orderBy $limit";exit;
        
        $eDao = new EmailDAO();
        $cursor = $eDao->queryCursor( "$select_fields $sql $orderBy $limit", $params);
        
        
        $lr = ListResponse::fillByCursor(0, $this->getRows(), $cursor, array(
            'email_id',
            'user_id',
            'company_id',
            'person_id',
            'identity_id',
            'connector_id',
            'connector_imapfolder_id',
            'attributes',
            'message_id',
            'spam',
            'incoming',
            'from_name',
            'from_email',
            'subject',
//             'text_content',
            'received',
            'deleted',
            'status',
            'created',
            'date',
//             'search_id',
            'solr_mail_id',
            'confidential',
            'server_properties_checksum',
            'action',
            'mailbox_name',
            'folderName',
            'seen',
            'answered',
            'forwarded',
            'junk',
            'attachment_count'
        ));
        
        $objs = $lr->getObjects();
        for($x=0; $x < count($objs); $x++) {
            $objs[$x]['email_id'] = $objs[$x]['solr_mail_id'];
            $objs[$x]['mailbox_name'] = $objs[$x]['folderName'];
            $objs[$x]['has_file_attachments'] = $objs[$x]['attachment_count'] > 0 ? true : false;
        }
        $lr->setObjects( $objs );
        
        
        // limit set? => calculate rowcount
        if ($limit) {
            $rowCount = $eDao->queryValue("select count(*) $sql", $params );
            $lr->setRowCount( $rowCount );
            $lr->setStart( $this->getStart() );
        }
        
        return $lr;
    }
    
    protected function parseSearchString( $str ) {
        $inQuote = false;
        $token = '';
        $tokens = array();
        $tokenModified = '';
        for($x=0; $x < strlen($str); $x++) {
            
            if ($token == '' && $str[$x] == '+') {
                $tokenModified = '+';
                continue;
            }
            if ($token == '' && $str[$x] == '-') {
                $tokenModified = '-';
                continue;
            }
            
            $token .= $str[$x];
            
            if ($str[$x] == '"') {
                $inQuote = !$inQuote;
                
                if ($inQuote == false) {
                    $tokens[] = $tokenModified.trim( $token, ' "' );
                    $token = '';
                    $tokenModified = '';
                }
            }
            if ($inQuote == false && $str[$x] == ' ') {
                if (trim($token)) {
                    $tokens[] = $tokenModified.trim( $token );
                    
                    $token = '';
                    $tokenModified = '';
                }
            }
        }
        if (trim($token, '" '))
            $tokens[] = $tokenModified.trim($token, '" ');
        
        
        // date detected?
        for($x=0; $x < count($tokens); $x++) {
            $t = $tokens[$x];
            
            $matches = array();
            if (preg_match('/^\\d{4}-\\d{1,2}-\\d{1,2}$/', $t, $matches)) {
                list($y, $m, $d) = explode('-', $matches[0]);
                $tokens[] = sprintf('%4d_%02d_%02d', $y, $m, $d);
            }
            else if (preg_match('/^\\d{1,2}-\\d{1,2}-\\d{2,4}$/', $t, $matches)) {
                list($d, $m, $y) = explode('-', $matches[0]);
                $tokens[] = sprintf('%4d_%02d_%02d', $y, $m, $d);
            }
        }
        
        // build string
        $searchString= '';
        for($x=0; $x < count($tokens); $x++) {
            if ($x > 0)
                $searchString .= ' ';
            
            if (strpos($tokens[$x], '+') === 0) {
                $searchString .= '+"' . substr($tokens[$x], 1) . '"';
            }
            else if (strpos($tokens[$x], '-') === 0) {
                $searchString .= '-"' . substr($tokens[$x], 1) . '"';
            }
            else {
                $searchString .= '"' . $tokens[$x] . '"';
            }
        }
        
        return $searchString;
    }
    
    
    protected function getConnectorImapFolderIds( $folderName ) {
        // fill cache
        if ($this->mapConnectorImapFolders == null) {
            $this->mapConnectorImapFolders = array();
            
            $cifDao = new ConnectorImapfolderDAO();
            $cifs = $cifDao->readAll();
            
            foreach($cifs as $cif) {
                $fn = $cif->getFolderName();
                
                if (isset($this->mapConnectorImapFolders[$fn]) == false)
                    $this->mapConnectorImapFolders[$fn] = array();
                
                $this->mapConnectorImapFolders[$fn][] = $cif;
            }
        }
        
        $ids = array();
        
        if (isset($this->mapConnectorImapFolders[$folderName])) foreach($this->mapConnectorImapFolders[$folderName] as $cif) {
            $ids[] = $cif->getConnectorImapfolderId();
        }
     
        return $ids;
    }
    
    
    

    public function applyMailTabSettings(MailTabSettings $mts) {
        
        $this->searchMethod = 'OR';
        
        // apply default filter(s)? (e-mailadresses linked to company/person)
        
        $filters = $mts->getFilters();
        
        if ($mts->applyDefaultFilters()) {
            $defaultFilters = $mts->getDefaultFilters();
            $filters = array_merge($filters, $defaultFilters);
        }
        
        // other filters specified?
        foreach($filters as $filter) {
            if ($filter['filter_type'] == 'email') {
                $v = trim($filter['filter_value']);
                
                // unescape asterisks
                $v = str_replace('\\*', '%', $v);
                
                // @domainname.com? => prefix with asterisk
                if (strpos($v, '@') === 0) {
                    $v = '%'.$v;
                }
                
                $this->fromEmails[] = $v;
                $this->toEmails[] = $v;
            }
            
            if ($filter['filter_type'] == 'folder') {
                $v = trim($filter['filter_value']);
                $this->setFolderName( $v );
            }
        }
    }
    
    
    
    public function applyMailboxSearchSettings( MailboxSearchSettings $mss ) {
        $includeFilters = $mss->getIncludeFilters();
        
        foreach($includeFilters as $filter) {
            if ($filter['filter_type'] == 'email') {
                $v = solr_escapeTerm( trim($filter['filter_value']) );
                // unescape asterisks
                $v = str_replace('\\*', '%', $v);
                
                // @domainname.com? => prefix with asterisk
                if (strpos($v, '@') === 0) {
                    $v = '%'.$v;
                }
                
                $this->toEmails[] = $v;
                $this->fromEmails[] = $v;
            }
            
            if ($filter['filter_type'] == 'folder') {
                $v = trim($filter['filter_value']);
                
                $this->mailboxNames[] = $v;
            }
            
            if ($filter['filter_type'] == 'action') {
                $v = trim($filter['filter_value']);
                
                $this->actions[] = $v;
            }
        }
        
        
        // exclude through faces
        $excludeFilters = $mss->getExcludeFilters();
        foreach($excludeFilters as $filter) {
            if ($filter['filter_type'] == 'email') {
                $v = trim($filter['filter_value']);
                // unescape asterisks
                $v = str_replace('\\*', '%', $v);
                
                // @domainname.com? => prefix with asterisk
                if (strpos($v, '@') === 0) {
                    $v = '%'.$v;
                }
                
                $this->exclToEmails[] = $v;
                $this->exclFromEmails[] = $v;
            }
            
            if ($filter['filter_type'] == 'folder') {
                $v = trim($filter['filter_value']);
                
                // query specific on mailbox? => skip exclusion
                $skip = false;
                foreach($this->mailboxNames as $mn) {
                    if ($v == $mn) {
                        $skip = true;
                        continue;
                    }
                }
                if ($skip) continue;
                
                    
                $this->exclMailboxNames[] = $v;
            }
            
            if ($filter['filter_type'] == 'action') {
                $v = trim($filter['filter_value']);
                
                $this->exclAction[] = $v;
            }
        }
    }
    
    
    
    
    public function readById($id) {
        $eDao = new EmailDAO();
        $o = $eDao->readBySolrMailId( $id );

        if (count($o) == 0) {
            return null;
        }
        
        $etDao = new EmailToDAO();
        $ets = $etDao->readByEmail( $o[0]->getEmailId() );
        $o[0]->setRecipients( $ets );
        
        $r = new MysqlMailRender();
        $r->setEmail( $o[0] );
        
        return $r;
    }


    
    
}

