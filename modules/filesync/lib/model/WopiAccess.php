<?php


namespace filesync\model;


use phpDocumentor\Reflection\Types\Parent_;

class WopiAccess extends base\WopiAccessBase {
    
    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setWritable(true);
    }


}

