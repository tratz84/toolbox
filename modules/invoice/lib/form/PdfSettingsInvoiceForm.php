<?php


namespace invoice\form;


use core\forms\BaseForm;
use core\forms\TextField;

class PdfSettingsInvoiceForm extends BaseForm {
    
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addWidget(new TextField('footer_note', null, t('Footer note')) );
        
    }
    
    
}
