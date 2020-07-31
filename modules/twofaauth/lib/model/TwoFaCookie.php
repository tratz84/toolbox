<?php


namespace twofaauth\model;


use twofaauth\model\base\TwoFaCookieBase;

class TwoFaCookie extends base\TwoFaCookieBase {

    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setActivated( false );
        
    }

}

