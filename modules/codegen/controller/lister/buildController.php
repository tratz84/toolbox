<?php


use core\controller\BaseController;

class buildController extends BaseController {
    
    
    public function action_index() {
        
        $cle = new \codegen\lister\CodegenListerEditor();
        
        
        
        $this->cle = $cle;
        
        return $this->render();
    }
    
    
}
