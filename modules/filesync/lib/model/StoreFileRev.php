<?php


namespace filesync\model;


class StoreFileRev extends base\StoreFileRevBase {

    
    
    public function getLastmodifiedFormat($f='d-m-Y H:i:s') {
        return format_datetime($this->getLastmodified(), $f);
    }
    
    
    public function asArray() {
        $arr = array();
        
        $arr['id'] = $this->getStoreFileRevId();
        $arr['filesize'] = $this->getFilesize();
        $arr['md5sum'] = $this->getMd5sum();
        $arr['revision'] = $this->getRev();
        $arr['lastmodified'] = $this->getLastmodified();
        $arr['encrypted'] = $this->getEncrypted() ? true : false;
        
        return $arr;
    }

}

