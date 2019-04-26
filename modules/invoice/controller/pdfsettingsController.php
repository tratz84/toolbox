<?php



use core\controller\BaseController;
use invoice\form\InvoicePdfSettingsForm;

class pdfsettingsController extends BaseController {
    
    
    public function action_index() {
        $settings = object_meta_get('invoice-pdfsettings', 0, 'color');
        
        $form = new InvoicePdfSettingsForm();
        $form->bind($settings);
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            $settings = $form->asArray();
            
            object_meta_save('invoice-pdfsettings', 0, 'color', $settings);

            redirect('/?m=invoice&c=settings');
        }
        
        
        $this->form = $form;
        
        return $this->render();
    }
    
    
}
