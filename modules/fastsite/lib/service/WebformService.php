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
        $wDao = new WebformDAO();
        $wfDao = new WebformFieldDAO();
        
        $id = $form->getWidgetValue('webform_id');
        if ($id) {
            $webform = $wDao->read($id);
        } else {
            $webform = new Webform();
        }
        
        $form->fill($webform, array('active', 'webform_name', 'webform_code', 'confirmation_message'));
        $webform->save();
        
        
        $oldFields = $wfDao->readByForm($webform->getWebformId());
        
        $fields = $form->getWebformFields();
        for($x=0; $x < count($fields); $x++) {
            $f = $fields[$x];
            
            if ($x < count($oldFields)) {
                $wf = $oldFields[$x];
            } else {
                $wf = new WebformField();
            }
            
            $wf->setInputField($f['class']);
            $wf->setValidator($f['validator']);
            $wf->setLabel($f['fieldname']);
            $wf->setDefaultValue($f['placeholder']);
            $wf->setWebformId($webform->getWebformId());
            $wf->setSort($x);
            
            if (isset($f['inputoptions'])) {
                $wf->setInputOptions(json_encode($f['inputoptions']));
            } else {
                $wf->setInputOptions('');
            }
            
            $wf->save();
        }
        
        // delete old
        for($x=count($fields); $x < count($oldFields); $x++) {
            $oldFields[$x]->delete();
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
