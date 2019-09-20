<?php

namespace fastsite\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use fastsite\model\WebformDAO;
use fastsite\model\WebformFieldDAO;
use fastsite\model\Webform;
use fastsite\form\WebformForm;

class WebformService extends ServiceBase {
    
    
    public function readWebform($webformId) {
        
        $wDao = new WebformDAO();
        $wfDao = new WebformFieldDAO();
        
        $webform = $wDao->read($webformId);
        
        $fields = $wfDao->readByForm($webform->getWebformId());
        $webform->setWebformFields( $fields );
        
        return $webform;
    }
    
    
    
    public function saveWebform(WebformForm $form) {
        
        // TODO: implement
        
    }
    
    
    public function searchForms($start, $limit, $opts=array()) {
        $fDao = new WebformDAO();
        
        $cursor = $fDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('form_id', 'form_name', 'form_code', 'active', 'edited', 'created'));
        
        return $r;
    }
    
    
    
}
