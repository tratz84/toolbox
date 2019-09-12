<?php


use core\controller\BaseController;
use fastsite\form\MediaFileform;

class mediaController extends BaseController {
    
    
    public function action_index() {
        
        
        return $this->render();
    }
    
    
    public function action_upload() {
        
        $this->form =  new MediaFileform();
        
        if (is_post()) {
            
        }
        
        
        return $this->render();
    }
    
    
    public function action_delete() {
        
    }
    
    
}
