<?php


namespace payment\form;


use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\HiddenField;
use core\forms\SelectField;

class PaymentTotalsForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new HiddenField('m', 'report'));
        $this->addWidget(new HiddenField('c', 'report'));
        $this->addWidget(new HiddenField('controllerName', 'payment@report/paymentTotals'));
        
//         $this->addWidget(new DatePickerField('start', '', 'Startdatum'));
//         $this->addWidget(new DatePickerField('end', '', 'Einddatum'));
        
        if (ctx()->isModuleEnabled('invoice')) {
            $this->addInvoiceStatus();
        }
        
        
    }
    
    protected function addInvoiceStatus() {
        
        $invoiceService = ObjectContainer::getInstance()->get(\invoice\service\InvoiceService::class);
        
        $is = $invoiceService->readAllInvoiceStatus();
        $mapIs = array();
        $mapIs[''] = 'Maak uw keuze';
        foreach($is as $i) {
            $mapIs[$i->getInvoiceStatusId()] = $i->getDescription();
        }
        
        
        $this->addWidget(new SelectField('invoice_status_id', '', $mapIs, 'Factuurstatus'));
        
    }
    
    
}

