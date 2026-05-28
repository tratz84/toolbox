<?php


namespace filesync\model;


class WopiAccess extends base\WopiAccessBase {
    
    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setWritable(true);
        $this->setTokenType('default');
    }


}

