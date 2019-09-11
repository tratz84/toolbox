<?php

namespace fastsite;


use core\controller\BaseController;
use fastsite\template\FastsiteTemplateLoader;
use fastsite\data\FastsiteSettings;

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
        
        $ts = $fastsiteSettings->getActiveTemplateSettings();
        
        $fth = object_container_get( FastsiteTemplateLoader::class );
        
        readfile( $fth->getFile($ts->getDefaultTemplateFile()) );
        
    }
    
    
}