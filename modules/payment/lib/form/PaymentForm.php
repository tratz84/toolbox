<?php

namespace payment\form;


use base\service\CompanyService;
use base\service\PersonService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\DynamicSelectField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\SelectField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;
use payment\model\Payment;
use payment\service\PaymentService;

class PaymentForm extends BaseForm {
    
    
    
    public function __construct() {
        
        parent::__construct();
        
        $this->addWidget( new HiddenField('payment_id') );
        
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
    
    
    public function bind($obj) {
        parent::bind($obj);
        
        $companyId = null;
        $personId = null;
        
        $customerWidget = $this->getWidget('customer_id');
        
        if (is_a($obj, Payment::class)) {
            $companyId = $obj->getCompanyId();
            $personId = $obj->getPersonId();
        }
        
        
        if (is_array($obj) && isset($obj['customer_id'])) {
            
            if (strpos($obj['customer_id'], 'company-') === 0) {
                $companyId = str_replace('company-', '', $obj['customer_id']);
            }
            else if (strpos($obj['customer_id'], 'person-') === 0) {
                $personId = str_replace('person-', '', $obj['customer_id']);
            }
            
        }
        
        if ($companyId) {
            $customerWidget->setValue('company-'.$companyId);
            
            $cs = ObjectContainer::getInstance()->get(CompanyService::class);
            $name = $cs->getCompanyName($companyId);
            
            $customerWidget->setDefaultText( $name );
        }
        else if ($personId) {
            $customerWidget->setValue('person-'.$personId);
            
            $ps = ObjectContainer::getInstance()->get(PersonService::class);
            $fullname = $ps->getFullname($personId);
            
            $customerWidget->setDefaultText( $fullname );
        }
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
