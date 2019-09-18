<?php

namespace fastsite\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use fastsite\model\WebformDAO;

class WebformService extends ServiceBase {
    
    
    
    public function searchForms($start, $limit, $opts=array()) {
        $fDao = new WebformDAO();
        
        $cursor = $fDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('form_id', 'form_name', 'form_code', 'active', 'edited', 'created'));
        
        return $r;
    }
    
    
    
}
