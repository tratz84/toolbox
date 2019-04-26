<?php


namespace webmail\model;


use core\forms\FormFileItem;

class EmailFile extends base\EmailFileBase implements FormFileItem {

    public function getId() {
        return $this->getEmailFileId();
    }
    
    public function getName() {
        return $this->getFilename();
    }
    
    public function getUrl() {
        return '';
    }
    
}

