<?php


use core\controller\BaseController;
use codegen\form\ListFormGeneratorForm;

class listformgeneratorController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new ListFormGeneratorForm();
        
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
        }
        
        
        
        
        return $this->render();
    }
    
    
    
}
