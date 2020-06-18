<?php


namespace webmail\model;


use core\exception\InvalidArgumentException;

class EmailTo extends base\EmailToBase {

    
    public function __construct($id=null) {
        parent::__construct($id);
        
        
        $this->setToType('To');
    }
    
    
    public function setToType($t) {
        $t = strtolower($t);
        
        if ($t == 'to') {
            return parent::setToType('To');
        } else if ($t == 'cc') {
            return parent::setToType('Cc');
        } else if ($t == 'bcc') {
            return parent::setToType('Bcc');
        } else {
            throw new InvalidArgumentException('Invalid to-type');
        }
    }

}

