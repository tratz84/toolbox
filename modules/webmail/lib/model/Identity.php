<?php


namespace webmail\model;


class Identity extends base\IdentityBase {

    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive( 1 );
    }

}

