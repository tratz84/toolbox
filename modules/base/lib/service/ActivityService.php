<?php

namespace base\service;

use base\model\ActivityDAO;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;

class ActivityService extends ServiceBase {
    
    
    public function search($start, $limit=20, $opts=array()) {
        $aDao = new ActivityDAO();
        
        $cursor = $aDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('activity_id', 'user_id', 'username', 'company_id', 'person_id', 'ref_object', 'ref_id', 'code', 'short_description', 'long_description', 'changes', 'created', 'company_name', 'firstname', 'insert_lastname', 'lastname'));
        
        return $r;
    }
    
    
    public function readForDashboard() {
        $aDao = new ActivityDAO();
        
        return $aDao->readLatest();
    }
    
    
    public function readActivity($activityId) {
        $aDao = new ActivityDAO();
        
        return $aDao->read($activityId);
    }
    
}

