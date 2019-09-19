<?php

namespace fastsite\service;

use core\service\ServiceBase;
use core\Context;
use fastsite\service\FastsiteSettingsService;

class WebsiteTemplateService extends ServiceBase {
    
    
    public function getTemplates() {
        $tsService = object_container_get(FastsiteSettingsService::class);
        
        
        $l = array();
        
        $templateDir = Context::getInstance()->getDataDir() . '/fastsite/templates';
        $datadir = Context::getInstance()->getDataDir();
        
        if (is_dir($templateDir)) {
            $files = list_files($templateDir);
            
            foreach($files as $f) {
                if (is_dir($templateDir.'/'.$f) == false) continue;
                
                $fullpath = realpath( $templateDir.'/'.$f );
                
                $relativePath = substr($fullpath, strlen(realpath($datadir))+1);
                
                $ts = $tsService->readTemplateSettingsByName( $f );
                
                $settings = array(
                    'fullpath' => $fullpath,
                    'path' => $relativePath,
                    'active' => $ts && $ts->getActive() ? true : false
                );
                
                
                $l[$f] = $settings;
            }
        }
        
        return $l;
    }
    
}

