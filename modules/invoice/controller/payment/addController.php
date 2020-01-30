<?php


use core\controller\BaseController;
use invoice\form\NewPaymentForm;

class addController extends BaseController {
    
    
    public function action_index() {
        
        
        $this->form = new NewPaymentForm();
        
        
        
        
        return $this->render();
    }
    
    
}
