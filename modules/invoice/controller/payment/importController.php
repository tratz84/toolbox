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
                    
//                     $file = $this->form->getWidgetValue('file');
                    
                    $file = copy_data_tmp($_FILES['file']['tmp_name']);
                    
                    redirect('/?m=invoice&c=payment/import&a=csv&f='.urlencode($f));
                } else {
                    // TODO...
                    report_user_error('Filetype not recognized');
                    redirect('/?m=invoice&c=payment/import');
                }
            }
        }
        
        return $this->render();
    }
    
    
    public function action_csv() {
        
        
        
        return $this->render();
    }
    
    
}


