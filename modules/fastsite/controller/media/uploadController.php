<?php


use core\controller\BaseController;
use fastsite\form\MediaFileform;

class uploadController extends BaseController {
    
    
    public function action_index() {
        
        $this->form =  new MediaFileform();
        
        if (is_post()) {
            
        }
        
        
        return $this->render();
    }
    
}

