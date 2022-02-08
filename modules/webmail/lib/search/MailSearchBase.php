<?php


namespace webmail\search;

use core\exception\InvalidStateException;
use webmail\MailTabSettings;
use webmail\MailboxSearchSettings;




abstract class MailSearchBase {
    
    protected $start = 0;
    protected $rows = 25;
    protected $query = null;
    
    protected $folderName = null;
    protected $action = null;
    
    
    public function __construct() {
        
    }
    
    public abstract function getFolders( $opts=array() );
    public abstract function applyMailTabSettings(MailTabSettings $mts);
    public abstract function applyMailboxSearchSettings( MailboxSearchSettings $mss );
    public abstract function searchListResponse();
    public abstract function readById( $id );
    
    
    public function setStart($s) { $this->start = $s; }
    public function getStart() { return $this->start; }
    
    public function getRows() { return $this->rows; }
    public function setRows($r) { $this->rows = $r; }
    
    public function getQuery() { return $this->query; }
    public function setQuery($q) { $this->query = $q; }
    
    public function getFolderName() { return $this->folderName; }
    public function setFolderName($n) { $this->folderName = $n; }
    
    public function getAction() { return $this->action; }
    public function setAction($a) { $this->action = $a; }
    
    
    
    /**
     * 
     * @return \webmail\search\MailSearchBase
     */
    public static function getInstance() {
        $n = webmail_storage_engine();
        
        if ($n == 'solr') {
            return object_container_create( SolrMailSearch::class );
        }
        else if ($n == 'db') {
            return object_container_create( MysqlMailSearch::class );
        }
        else {
            throw new InvalidStateException( 'Unknown storage engine' );
        }
        
    }
    
    
}


