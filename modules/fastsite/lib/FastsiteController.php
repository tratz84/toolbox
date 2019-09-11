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
        
        // get template settings
        $templateFile = $ts->getDefaultTemplateFile();
        
        $tfs = $ts->getTemplateFileSettings( $templateFile );
        
        
        $parser = new FastsiteTemplateParser( $ts, $tfs );
        
        $parser->addVars( get_object_vars($this) );
        
        $parser->render();
    }
    
    
}