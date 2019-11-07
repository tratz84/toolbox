<?php


use core\controller\BaseController;
use codegen\form\ListEditGeneratorForm;

class listeditgeneratorController extends BaseController {
    
    
    
    public function action_index() {
        
        $this->form = new ListEditGeneratorForm();
        
        return $this->render();
    }
    
    
}
