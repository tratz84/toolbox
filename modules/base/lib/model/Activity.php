<?php


namespace base\model;


class Activity extends base\ActivityBase {

    
    public function setChanges($p) {
        parent::setChanges(serialize($p));
    }
    
    public function getCreatedFormat($f='d-m-Y H:i:s') {
        return format_datetime($this->getCreated(), $f);
    }
    

}

