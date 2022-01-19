<?php


namespace project\model;


class ProjectHour extends base\ProjectHourBase {

    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setDeclarable(true);
        $this->setRegistrationType('from_to');
    }
    
    
    public function setDeclarable($p) {
        if ($p == 'y') $p = true;
        else if ($p == 'n') $p = false;
        
        return parent::setDeclarable($p);
    }
    
    public function setDuration($d) {
        return parent::setDuration( strtodouble($d) );
    }
    

}

