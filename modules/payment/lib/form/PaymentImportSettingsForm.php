<?php


namespace payment\form;


use core\forms\BaseForm;
use payment\service\PaymentService;
use core\forms\SelectField;

class PaymentImportSettingsForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addPaymentMethods();
        
    }
    
    
    protected function addPaymentMethods() {
        
        $paymentService = object_container_get(PaymentService::class);
        
        $methods = $paymentService->readAllMethods();
        $map = array();
        $map[''] = 'Maak uw keuze';
        
        foreach($methods as $m) {
            $map[$m->getPaymentMethodId()] = $m->getDescription();
        }
        
        $this->addWidget(new SelectField('import_payment_method_id', '', $map, 'Betaalmethode'));
    }
    
    
}

