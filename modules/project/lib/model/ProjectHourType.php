<?php


namespace project\model;


class ProjectHourType extends base\ProjectHourTypeBase {

    
    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setVisible(true);
    }

}

