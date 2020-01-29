<?php

namespace invoice\form;


use core\forms\BaseForm;
use core\forms\DynamicSelectField;
use core\forms\SelectField;
use invoice\service\PaymentService;
use core\forms\EuroField;

class NewPaymentForm extends BaseForm {
    
    
    
    public function __construct() {
        
        parent::__construct();
        
        
//         $this->addWidget(new TextField('t', '', 'Test'));
        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=base&c=customer&a=select2', 'Klant') );
        
        $this->addPaymentMethod();
        
        $this->addWidget(new EuroField('amount', '', t('Payment amount')));
        
    }
    
    
    protected function addPaymentMethod() {
        
        $ps = object_container_get(PaymentService::class);
        $methods = $ps->readActiveMethods();
        
        $map = array();
        $map[''] = t('Make your choice');
        foreach($methods as $m) {
            $map[ $m->getPaymentMethodId()] = $m->getDescription();
        }
        
        $this->addWidget(new SelectField('payment_method_id', '', $map, t('Payment method')));
        
        
    }
    
    
}
