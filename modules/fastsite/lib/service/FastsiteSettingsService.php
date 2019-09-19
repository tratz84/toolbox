<?php

namespace fastsite\service;


use core\service\ServiceBase;
use fastsite\model\TemplateSettingDAO;
use fastsite\form\TemplateSettingsForm;
use fastsite\model\TemplateSetting;
use core\exception\InvalidStateException;

class FastsiteSettingsService extends ServiceBase {
    
    public function readActiveTemplate() {
        $tsDao = new TemplateSettingDAO();
        
        $ts = $tsDao->readActive();
        
        if ($ts == null) {
            throw new InvalidStateException('No active template');
        }
        
        return $ts;
    }
    
    public function readTemplateSettingsByName($templateName) {
        $tsDao = new TemplateSettingDAO();
        
        $t = $tsDao->readByName($templateName);
        
        return $t;
    }
    
    
    public function saveTemplateSettings(TemplateSettingsForm $form) {
        
        $name = $form->getWidgetValue('template_name');
        
        $ts = $this->readTemplateSettingsByName($name);
        if ($ts === null) {
            $f = get_data_file('fastsite/templates/'.basename($name));
            if (!$f) {
                throw new InvalidStateException('Invalid template');
            }
            
            $ts = new TemplateSetting();
            $ts->setTemplateName($name);
        }
        
        $form->fill($ts, ['active']);
        
        // max 1 marked as active
        if ($ts->getActive()) {
            $tsDao = new TemplateSettingDAO();
            $tsDao->allActiveToFalse();
        }
        
        $ts->save();
        
        return $ts;
    }
    
    
}
