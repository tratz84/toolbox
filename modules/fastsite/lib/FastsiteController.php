<?php

namespace fastsite;


use core\controller\BaseController;
use fastsite\FastsiteTemplateHelper;

class FastsiteController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    
    public function render() {
        
        $fth = object_container_get( FastsiteTemplateHelper::class );
        
        print 'todo: render thingie..';
        
    }
    
    
}