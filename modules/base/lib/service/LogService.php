<?php

namespace base\service;


use base\model\ObjectLogDAO;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;

class LogService extends ServiceBase {
    

    public function search($start, $limit, $opts = array()) {
        $olDao = new ObjectLogDAO();
        
        $cursor = $olDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('object_log_id', 'object_name', 'object_id', 'object_key', 'object_action', 'value_old', 'value_new', 'created'));
        
        return $r;
    }
    
}
