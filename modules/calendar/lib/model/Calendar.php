<?php


namespace calendar\model;


class Calendar extends base\CalendarBase {

    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setDeleted(null);
        $this->setActive(true);
    }

}

