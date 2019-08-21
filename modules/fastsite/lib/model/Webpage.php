<?php


namespace fastsite\model;


class Webpage extends base\WebpageBase {

    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive( true );
    }

}

