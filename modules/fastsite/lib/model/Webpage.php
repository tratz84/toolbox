<?php


namespace fastsite\model;


class Webpage extends base\WebpageBase {

    protected $revision = null;
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive( true );
    }
    
    
    public function setRevision($r) { $this->revision = $r; }
    public function getRevision() { return $this->revision; }

}

