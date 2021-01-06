<?php
use base\service\SettingsService;
use core\controller\BaseController;
use invoice\InvoiceSettings;

class settingsController extends BaseController
{

    public function init()
    {
        checkCapability('base', 'edit-masterdata');
        
        $this->addTitle( strOrder(1) . ' ' . t('settings'));
    }

    public function action_index()
    {
        $settingsService = $this->oc->get(SettingsService::class);
        $this->settings = $settingsService->settingsAsMap();
        
        $this->invoiceSettings = $this->oc->get(InvoiceSettings::class);

        if (is_post()) {

            if (get_var('invoice__orderType') == 'order')
                $settingsService->updateValue('invoice__orderType', 'order');
            else
                $settingsService->updateValue('invoice__orderType', 'invoice');
            
            $settingsService->updateValue('invoice__offerTemplate', get_var('invoice__offerTemplate'));
            $settingsService->updateValue('invoice__invoiceTemplate', get_var('invoice__invoiceTemplate'));
            
            $settingsService->updateValue('invoice__intracommunautaire', get_var('invoice__intracommunautaire'));
            
            $settingsService->updateValue('invoice__prices_inc_vat', get_var('invoice__prices_inc_vat'));
            
            $settingsService->updateValue('invoice__invoice_date_check', get_var('invoice__invoice_date_check')?1:0);

            $settingsService->updateValue('invoice__billable_enabled', get_var('invoice__billable_enabled'));
            $settingsService->updateValue('invoice__billable_only_open', get_var('invoice__billable_only_open'));
            
                
            if (has_file('fileAttachmentOffer') || has_file('fileAttachmentInvoice')) {
                
                if (has_file('fileAttachmentOffer')) {
                    save_upload_to('fileAttachmentOffer', 'attachments/offer/');
                }
                if (has_file('fileAttachmentInvoice')) {
                    save_upload_to('fileAttachmentInvoice', 'attachments/invoice/');
                }
                
                report_user_message(t('Changes saved'));
                redirect('/?m=invoice&c=settings');
            }
            
            report_user_message(t('Changes saved'));
            redirect('/?m=invoice&c=settings');
        }

        $this->render();
    }
    
    
    
    
    public function action_delete_offer_file() {
        $file = basename( $_REQUEST['f'] );
        
        delete_data_file("attachments/offer/$file");
        
        redirect('/?m=invoice&c=settings');
    }
    
    
    
    public function action_delete_invoice_file() {
        $file = basename( $_REQUEST['f'] );
        
        delete_data_file("attachments/invoice/$file");
        
        redirect('/?m=invoice&c=settings');
    }
    
    
}