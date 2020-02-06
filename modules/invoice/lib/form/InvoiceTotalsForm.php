<?php


namespace invoice\form;


use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\ObjectContainer;
use invoice\service\InvoiceService;
use core\forms\SelectField;
use core\forms\HiddenField;

class InvoiceTotalsForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new HiddenField('m', 'report'));
        $this->addWidget(new HiddenField('c', 'report'));
        $this->addWidget(new HiddenField('controllerName', 'invoice@report/invoiceTotals'));
        
        $this->addWidget(new DatePickerField('start', '', 'Startdatum'));
        $this->addWidget(new DatePickerField('end', '', 'Einddatum'));
        
        $this->addInvoiceStatus();
        
        
    }
    
    protected function addInvoiceStatus() {
        
        $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
        
        $is = $invoiceService->readAllInvoiceStatus();
        $mapIs = array();
        $mapIs[''] = 'Maak uw keuze';
        foreach($is as $i) {
            $mapIs[$i->getInvoiceStatusId()] = $i->getDescription();
        }
        
        
        $this->addWidget(new SelectField('invoice_status_id', '', $mapIs, 'Status'));
        
    }
    
    
}

