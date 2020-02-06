<?php


use core\controller\BaseController;
use payment\form\PaymentImportForm;

class importController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new PaymentImportForm();
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                // TODO: handle
                $filetype = $this->form->getWidgetValue('filetype');
                
                if ($filetype == 'sheet') {
                    $ext = file_extension($_FILES['file']['name']);
                    $file = copy_data_tmp($_FILES['file']['tmp_name'], 'payment-import-'.date('YmdHis').'.'.$ext);
                    
                    redirect('/?m=payment&c=import/mapping&f='.urlencode(basename($file)));
                }
                
            }
        }
        
        return $this->render();
    }
    
    
}
