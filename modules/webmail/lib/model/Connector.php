<?php


namespace webmail\model;


class Connector extends base\ConnectorBase {

    protected $imapfolders = array();
    
    protected $filters = array();
    
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive( true );
    }
    
    public function getFilters() { return $this->filters; }
    public function setFilters($filters) { $this->filters = $filters; }
    
    public function getFolders() {
        $f = array();
        
        foreach($this->getImapfolders() as $if) {
            $f[] = $if->getFolderName();
        }
        
        return $f;
    }
    
    public function getImapfolders() { return $this->imapfolders; }
    public function setImapfolders($f) { $this->imapfolders = $f; }

}

