<?php

namespace payment\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\DatePickerField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;
use customer\forms\CustomerSelectWidget;
use payment\service\PaymentService;

class PaymentForm extends BaseForm {
    
    
    
    public function __construct() {
        
        parent::__construct();
        
        $this->addWidget( new HiddenField('payment_id') );
        
        $this->addWidget( new CustomerSelectWidget() );
        
        $this->addWidget( new DatePickerField('payment_date', '', 'Betaaldatum'));
        
        $this->addWidget( new CheckboxField('cancelled', '', 'Geannuleerd') );
        $this->getWidget('cancelled')->setInfoText('Uitgevoerde betaling om een of andere reden mislukt/geannuleerd?');
        
        $this->addWidget( new TextField('description', '', 'Omschrijving'));
        
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
            
            foreach($objs as $o) {
                $pid = (int)$o['payment_method_id'];
                if ($pid == 0) {
                    return 'Ongeldige betalingsmethode';
                }
            }
            
            // check if there's a line with an amount
            $hasAmount = false;
            foreach($objs as $o) {
                $cents = intval(strtodouble($o['amount']) * 100);
                
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
