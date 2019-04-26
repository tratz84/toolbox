<?php

namespace invoice\form;


use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;
use invoice\service\PaymentService;

class PaymentMethodForm extends BaseForm {
    
    
    
    
    public function __construct() {
        
        $this->addKeyField('payment_method_id');
        
        $this->addWidget( new HiddenField('payment_method_id', '', 'Id') );
        
        $this->addWidget( new CheckboxField('active', '', 'Actief'));
        $this->addWidget( new CheckboxField('default_selected', '', 'Standaard gekozen'));
        $this->addWidget( new TextField('code', '', 'Code', ['maxlength' => 16]) );
        $this->addWidget( new TextField('description', '', 'Omschrijving') );
        $this->addWidget( new TextareaField('note', '', 'Notitie') );
        
        $this->addValidator('description', new NotEmptyValidator());
        
        $this->addValidator('code', new NotEmptyValidator());
        $this->addValidator('code', function($form) {
            $code = $form->getWidgetValue('code');
            if (trim($code) == '')
                return null;
                
            $ps = ObjectContainer::getInstance()->get(PaymentService::class);
            
            $pm = $ps->readMethodByCode( $code );
            
            if ($pm && $pm->getPaymentMethodId() != $form->getWidgetValue('payment_method_id')) {
                return 'Reeds in gebruik';
            }
            
            return null;
        });
        
    }
    
}

