<?php

namespace fastsite;


use core\controller\BaseController;
use fastsite\template\FastsiteTemplateLoader;
use fastsite\data\FastsiteSettings;
use fastsite\template\FastsiteTemplateParser;

class FastsiteController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function render404() {
        http_response_code(404);
        
        // TODO set 404 template
        $this->content = 'Page not found';
        
        print $this->content;
        
//         $this->render();
    }
    
    public function render() {
        
        $fastsiteSettings = object_container_get(FastsiteSettings::class);
        
        // get fastsite settings
        $ts = $fastsiteSettings->getActiveTemplateSettings();
        
        // TemplateFileSettings
        $tfs = null;
        
        // get Website-Template-File for current webpage
        $wtf = $this->webpage->getFastsiteTemplateFile();
        if ($wtf) {
            $tfs = $ts->getTemplateFileSettings( $wtf );
        }
        
        // fallback to default template
        if ($tfs == null) {
            $templateFile = $ts->getDefaultTemplateFile();
            
            $tfs = $ts->getTemplateFileSettings( $templateFile );
        }
        
        
        // execute template builder
        $parser = new FastsiteTemplateParser( $ts, $tfs );
        
        $parser->addVars( get_object_vars($this) );
        
        $parser->render();
    }
    
    
}