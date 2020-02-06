<?php

namespace invoice\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HtmlField;
use core\forms\WidgetContainer;
use function t;
use invoice\service\InvoiceService;
use core\ObjectContainer;
use core\forms\HiddenField;

class RecentInvoiceWidgetForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->disableSubmit();
        
        $this->addWidget(new HiddenField('save', '1'));
        
        $this->addInvoiceStatuses();
        
        $this->addWidget(new CheckboxField('show_open_days', '', 'Aantal dagen tonen'));
        $this->getWidget('show_open_days')->setInfoText('Kolom toevoegen met aantal dagen van ouderdom factuur');
        $this->addWidget(new CheckboxField('show_invoice_amount', '', strOrder(1).'bedrag tonen'));
        
    }
    
    public function bind($obj) {
        parent::bind($obj);
        
        if (isset($obj['save']) == false) {
            if (isset($obj['invoiceStatusIds'])) {
                foreach($obj['invoiceStatusIds'] as $id) {
                    $w = $this->getWidget('invoice_status_'.$id);
                    
                    if ($w) {
                        $w->setValue(1);
                    }
                }
            } else {
                $w = $this->getWidget('invoice-statuses');
                foreach($w->getWidgets() as $w2) {
                    if (strpos($w2->getName(), 'invoice_status_') === 0) {
                        $w2->setValue(1);
                    }
                }
            }
        }
        
        
    }
    
    public function getSelectedInvoiceStatusIds() {
        $r = array();
        
        $invoiceStatusContainer = $this->getWidget('invoice-statuses');
        
        foreach($invoiceStatusContainer->getWidgets() as $w) {
            if (strpos($w->getName(), 'invoice_status_') === 0) {
                if ($w->getValue()) {
                    $r[] = $w->getField('invoice_status_id');
                }
            }
        }
        
        return $r;
    }
    
    protected function addInvoiceStatuses() {
        
        $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
        $invoiceStatuses = $invoiceService->readActiveInvoiceStatus();
        
        $wc = new WidgetContainer();
        $wc->setName('invoice-statuses');
        
        $wc->addWidget(new HtmlField('', '', 'Getoonde statussen'));
        
        foreach($invoiceStatuses as $is) {
            $w = new CheckboxField('invoice_status_' . $is->getInvoiceStatusId(), '', $is->getDescription());
            $w->setField('invoice_status_id', $is->getInvoiceStatusId());
            $wc->addWidget($w);
        }
        
        $this->addWidget($wc);
    }
    
    
}
