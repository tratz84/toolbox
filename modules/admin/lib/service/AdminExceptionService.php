<?php

namespace admin\service;



use admin\model\ExceptionLogDAO;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use core\exception\InvalidStateException;

class AdminExceptionService extends ServiceBase {
    
    
    
    
    public function search($start, $limit, $opts = array()) {
        if (isset($opts['contextNames']) && is_array($opts['contextNames']) && count($opts['contextNames']) == 0)
            throw new InvalidStateException('No contextname linked to manager');
        
        $elDao = new ExceptionLogDAO();
        
        $cursor = $elDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('exception_log_id', 'contextName', 'user_id', 'request_uri', 'message', 'created'));
        
        return $r;
    }
    
    public function readException($id) {
        $elDao = new ExceptionLogDAO();
        
        return $elDao->read($id);
    }
    
    
}