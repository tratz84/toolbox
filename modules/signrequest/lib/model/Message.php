<?php


namespace signrequest\model;


class Message extends base\MessageBase {

    protected $signers = array();
    protected $files = array();
    
    
    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setSent( false );
    }
    
    
    public function getSigners() { return $this->signers; }
    public function setSigners($arr) {
        $this->signers = $arr;
    }
    
    public function getFiles() { return $this->files; }
    public function setFiles($f) { $this->files = $f; }

}

