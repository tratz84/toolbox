<?php

namespace payment\form;


use core\forms\BaseForm;
use core\forms\DynamicSelectField;
use core\forms\SelectField;
use core\forms\validator\NotEmptyValidator;
use core\forms\DatePickerField;
use core\forms\TextareaField;
use core\forms\HtmlField;

class PaymentForm extends BaseForm {
    
    
    
    public function __construct() {
        
        parent::__construct();
        
        
        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=base&c=customer&a=select2', 'Klant') );
        
        $this->addWidget( new DatePickerField('payment_date', '', 'Betaaldatum'));
        
        $this->addWidget( new HtmlField('spacer', '', ''));
        
        $this->addWidget( new PaymentLineListEdit() );
        
        $this->addWidget( new TextareaField('note', '', 'Notitie'));
        
        
        $this->addValidator('customer_id', new NotEmptyValidator());
        
        $this->addValidator('PaymentLines', function($form) {
            $w = $form->getWidget('PaymentLines');
            
            $objs = $w->getObjects();
            
            if (count($objs) == 0) {
                return 'Geen betaalregels toegevoegd';
            }
            
            // check if there's a line with an amount
            $hasAmount = false;
            foreach($objs as $o) {
                $cents = intval(strtodouble($o['euro']) * 100);
                
                if ($cents != 0) {
                    $hasAmount = true;
                    break;
                }
            }
            
            if ($hasAmount == false) {
                return 'Geen betaalregel met een bedrag';
            }
            
        });
        
        
//         $this->addPaymentMethod();
        
//         $this->addWidget(new EuroField('amount', '', t('Payment amount')));
        
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
