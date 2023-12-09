<?php


namespace base\model;


class Cron extends base\CronBase {
    
    protected $title;
    
    protected $cronClass;

    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setRunning(false);
    }
    
    public function setTitle($t) { $this->title = $t; }
    public function getTitle() { return $this->title; }
    
    public function setCronClass( $c ) { $this->cronClass = $c; }
    public function getCronClass() { return $this->cronClass; }
    
    public function isForceStartAllowed() {
        if ( $this->cronClass ) {
            $c = new $this->cronClass();
            return $c->isForceStartAllowed();
        }
        
        return null;
    }
    
    public function getLastRunFormat($f='d-m-Y H:i:s') {
        
        return format_date($this->getLastRun(), $f);
        
    }

}

