<?php

namespace fastsite;


use core\controller\BaseController;
use fastsite\FastsiteTemplateHelper;

class FastsiteController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function render404() {
        http_response_code(404);
        
        // TODO set 404 template
        $this->content = 'Page not found';
        
        $this->render();
    }
    
    public function render() {
        $fth = object_container_get( FastsiteTemplateHelper::class );
        
        readfile( $fth->getFile('/index.html') );
        
//         print 'todo: render thingie..';
    }
    
    
}