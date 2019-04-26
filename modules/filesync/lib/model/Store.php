<?php


namespace filesync\model;


class Store extends base\StoreBase {


    
    
    public function asArray() {
        $arr = array();
        
        $arr['id'] = $this->getStoreId();
        $arr['type'] = $this->getStoreType();
        $arr['name'] = $this->getStoreName();
        $arr['last_file_change'] = $this->getLastFileChange();
        
        return $arr;
    }
}

