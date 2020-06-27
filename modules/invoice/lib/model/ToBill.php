<?php


namespace invoice\model;


class ToBill extends base\ToBillBase {

    public function __construct($id=null) {
        parent::__construct($id);
        
    }
    
    
    public function setCompanyId($p) {
        return parent::setCompanyId( $p ? $p : null );
    }
    
    public function setPersonId($p) {
        return parent::setPersonId( $p ? $p : null );
    }

}

