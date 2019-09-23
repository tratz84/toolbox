<?php

namespace fastsite\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use fastsite\model\WebformDAO;
use fastsite\model\WebformFieldDAO;
use fastsite\model\Webform;
use fastsite\form\WebformForm;
use fastsite\model\WebformField;

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
        
        $id = $form->getWidgetValue('webform_id');
        $wDao = new WebformDAO();
        if ($id) {
            $webform = $wDao->read($id);
        } else {
            $webform = new Webform();
        }
        
        $form->fill($webform, array('active', 'webform_name', 'webform_code', 'confirmation_message'));
        $webform->save();
        
        $fields = $form->getWebformFields();
        foreach($fields as $f) {
            $wf = new WebformField();
            $wf->setInputField($f['class']);
            $wf->setValidator($f['validator']);
            $wf->setLabel($f['fieldname']);
            $wf->setDefaultValue($f['placeholder']);
            $wf->setWebformId($webform->getWebformId());
            $wf->save();
        }
        
        return $webform;
    }
    
    
    public function searchForms($start, $limit, $opts=array()) {
        $fDao = new WebformDAO();
        
        $cursor = $fDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('form_id', 'form_name', 'form_code', 'active', 'edited', 'created'));
        
        return $r;
    }
    
    
    
}
