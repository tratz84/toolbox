<?php


use core\controller\BaseController;
use codegen\form\FormGeneratorForm;

class formgeneratorController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new FormGeneratorForm();
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            $this->form->validate();
            
            // TODO: save
            
        }
        
        
        return $this->render();
    }
    
    
}

