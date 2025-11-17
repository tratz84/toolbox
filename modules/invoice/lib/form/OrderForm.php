<?php

namespace invoice\form;

use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\MinimumLinesValidator;
use core\forms\validator\NotContainsValidator;
use core\forms\validator\NotEmptyValidator;
use customer\forms\CustomerTableSelectWidget;
use invoice\service\OrderService;

class OrderForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('order_id');
        
        $this->addJavascript('edit-order', '/js/invoice/editOrder.js');
        
        $this->addWidget( new HiddenField('order_id', '', 'Id') );
        
        //         $this->addWidget( new CheckboxField('accepted', '', 'Akkoord') );
        
        $this->addWidget( new HtmlField('orderNumberText', '', 'Ordernummer'));
        
        $this->addWidget( new DatePickerField('order_date', '', 'Datum') );
        
        $this->addWidget( new CustomerTableSelectWidget() );
        //         $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=customer&c=customer&a=select2', 'Klant') );
        
        $this->addOrderStatus();
        $this->addWidget( new TextField('subject', '', 'Betreft') );
        
        
        $this->addWidget( new ListOrderLineWidget('orderLines') );
        
        //         $this->addWidget( new EuroField('deposit', '', 'Waarborgsom') );
        //         $this->addWidget( new EuroField('payment_upfront', '', 'Vooraf te betalen') );
        
        $this->addWidget( new TextareaField('comment', '', 'Opmerking onder order') );
        $this->addWidget( new TextareaField('note', '', 'Interne notitie') );
        
        $this->addValidator('customer_id', new NotContainsValidator(array('', '0', 'Maak uw keuze')));
        $this->addValidator('subject', new NotEmptyValidator());
        $this->addValidator('orderLines', new MinimumLinesValidator());
    }
    
    protected function addOrderStatus() {
        
        $orderService = ObjectContainer::getInstance()->get(OrderService::class);
        $status = $orderService->readActiveOrderStatus();
        
        $map = array();
        $defaultSelectedId = null;
        foreach($status as $s) {
            if ($defaultSelectedId == null || $s->getDefaultSelected()) {
                $defaultSelectedId = $s->getOrderStatusId();
            }
            
            $map[$s->getOrderStatusId()] = $s->getDescription();
        }
        
        $this->addWidget( new SelectField('order_status_id', $defaultSelectedId, $map, 'Status') );
    }
    
}

