<?php


namespace project\model;


class ProjectHour extends base\ProjectHourBase {

    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setDeclarable(true);
        $this->setRegistrationType('from_to');
    }

}

