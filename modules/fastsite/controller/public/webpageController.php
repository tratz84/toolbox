<?php


use core\controller\BaseController;
use fastsite\FastsiteController;

class webpageController extends FastsiteController {
    
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    
    public function action_index() {
        
        // TODO: ...
        print 'webpage index public';
        
        $this->render();
    }
    
    
}
