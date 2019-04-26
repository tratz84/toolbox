<?php


namespace admin\model;


class ExceptionLog extends base\ExceptionLogBase {

    
    public function setMessage($msg) {
        if (strlen($msg) > 255) {
            $msg = substr($msg, 0, 255);
        }
        
        parent::setMessage( $msg );
    }
    
    public function skipLogging() {
        
        // skip favicon errors
        if (strpos($this->getRequestUri(), 'favicon.ico') !== false)
            return true;
        
        // user is requesting an invalid url on purpose, don't log..
        if ($this->getMessage() == 'customer not found')
            return true;
        
        return false;
    }
    
    
    public function save() {
        
        if ($this->skipLogging()) {
            return false;
        }
        
        
        
        return parent::save();
    }
    
}

