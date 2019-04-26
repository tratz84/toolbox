<?php


namespace base\model;


class CronRun extends base\CronRunBase {


    public function getCreatedFormat($f='d-m-Y H:i:s') {
        return format_date($this->getCreated(), $f);
    }
    
}

