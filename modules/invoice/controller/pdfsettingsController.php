<?php



use core\controller\BaseController;
use invoice\form\PdfSettingsInvoiceForm;
use invoice\form\PdfSettingsOfferForm;

class pdfsettingsController extends BaseController {
    
    /**
     * pdf settings for offer's. Please note that the naming is kinda sh*tty
     */
    public function action_offer() {
        $settings = object_meta_get('invoice-pdfsettings-offer', 0, 'color');
        if (is_array($settings) == false) $settings = array();
        
        $form = new PdfSettingsOfferForm();
        $form->bind($settings);
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            $settings = $form->asArray();
            
            object_meta_save('invoice-pdfsettings-offer', 0, 'color', $settings);

            report_user_message(t('Changes saved'));
            redirect('/?m=invoice&c=pdfsettings&a=offer');
        }
        
        
        $this->form = $form;
        
        return $this->render();
    }

    
    public function action_invoice() {
        $settings = object_meta_get('invoice-pdfsettings-invoice', 0, 'settings');
        if (is_array($settings) == false) $settings = array();
        
        $form = new PdfSettingsInvoiceForm();
        $form->bind($settings);
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            $settings = $form->asArray();
            
            object_meta_save('invoice-pdfsettings-invoice', 0, 'settings', $settings);
            
            report_user_message(t('Changes saved'));
            redirect('/?m=invoice&c=pdfsettings&a=invoice');
        }
        
        
        $this->form = $form;
        
        return $this->render();
    }
    
    
}
