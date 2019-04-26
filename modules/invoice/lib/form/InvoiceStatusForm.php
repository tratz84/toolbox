<?php

namespace invoice\form;


use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;
use invoice\service\InvoiceService;

class InvoiceStatusForm extends BaseForm {
    
    
    
    
    public function __construct() {
        
        $this->addKeyField('invoice_status_id');
        
        $this->addWidget( new HiddenField('invoice_status_id', '', 'Id') );
        
        $this->addWidget( new CheckboxField('active', '', 'Actief'));
        $this->addWidget( new TextField('description', '', 'Omschrijving') );
        $this->addWidget( new CheckboxField('default_selected', '', 'Standaard gekozen'));

        $this->addValidator('description', new NotEmptyValidator());
        
        $this->addValidator('description', function($form) {
            $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
            
            $id = $form->getWidgetValue('invoice_status_id');
            $desc = $form->getWidgetValue('description');
            
            $iss = $invoiceService->readAllInvoiceStatus();
            foreach($iss as $is) {
                if (strtolower($is->getDescription()) == strtolower($desc) && $is->getInvoiceStatusId() != $id) {
                    return 'Omschrijving bestaat reeds';
                }
            }
            
            return null;
        });
    }
    
}

