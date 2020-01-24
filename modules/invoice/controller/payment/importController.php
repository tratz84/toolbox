<?php



use core\controller\BaseController;
use invoice\form\PaymentImportForm;

class importController extends BaseController {
    
    
    
    public function action_index() {
        $this->form = new PaymentImportForm();
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            if ($this->form->validate()) {
                if ($this->form->isCsv()) {
                    
                } else {
                    // TODO...
                }
            }
        }
        
        return $this->render();
    }
    
    
    public function action_csv() {
        
        
        
        return $this->render();
    }
    
    
}


