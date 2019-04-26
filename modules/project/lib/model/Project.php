<?php


namespace project\model;


class Project extends base\ProjectBase {

    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive(true);
    }
    

}

