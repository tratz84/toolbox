<?php

namespace fastsite;

use core\service\ServiceBase;
use core\Context;

class WebsiteTemplateService extends ServiceBase {
    
    
    public function getTemplates() {
        $l = array();
        
        $templateDir = Context::getInstance()->getDataDir() . '/fastsite/templates';
        $datadir = Context::getInstance()->getDataDir();
        
        if (is_dir($templateDir)) {
            $files = list_files($templateDir);
            
            foreach($files as $f) {
                if (is_dir($templateDir.'/'.$f) == false) continue;
                
                $fullpath = realpath( $templateDir.'/'.$f );
                
                $relativePath = substr($fullpath, strlen(realpath($datadir))+1);
                
                $l[$f] = array(
                    'fullpath' => $fullpath,
                    'path' => $relativePath
                );
            }
        }
        
        return $l;
    }
    
}

