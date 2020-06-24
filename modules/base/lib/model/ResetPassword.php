<?php


namespace base\model;


class ResetPassword extends base\ResetPasswordBase {

    
    public function __construct($id=null)  {
        parent::__construct( $id );
        
        $this->setUsed( null );
        
    }

    
    
    
    public function getAgeInSeconds() {
        return time() - date2unix( $this->getCreated() );
    }
    
    
}

