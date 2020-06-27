<?php

namespace invoice\form;



use customer\service\CompanyService;
use customer\service\PersonService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\DynamicSelectField;
use core\forms\EuroField;
use core\forms\HiddenField;
use core\forms\NumberField;
use core\forms\TextField;
use core\forms\validator\DoubleNumberValidator;
use core\forms\validator\NotEmptyValidator;
use invoice\model\ToBill;
use core\forms\DoubleField;
use core\forms\TextareaField;
use core\forms\SelectField;
use customer\forms\CustomerSelectWidget;

class ToBillForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('to_bill_id');
        
        $this->addWidget(new HiddenField('to_bill_id'));
        $this->addWidget(new CheckboxField('paid', '', t('Paid')));
        $this->addWidget(new SelectField('type', '', ['' => t('Make your choice'), 'credit' => t('Credit'), 'debet' => t('Debet')], t('Type')));
        $this->addWidget(new CustomerSelectWidget());
//         $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=customer&c=customer&a=select2', 'Klant') );
        
        $this->addWidget(new TextField('short_description', '', 'Korte omschrijving'));
        $this->addWidget(new DoubleField('amount', '', 'Aantal'));
        $this->addWidget(new EuroField('price', '', 'Bedrag'));
        
        $this->addWidget(new TextareaField('long_description', '', 'Notitie'));
        
        $this->addValidator('type', new NotEmptyValidator());
        $this->addValidator('customer_id', new NotEmptyValidator());
        $this->addValidator('short_description', new NotEmptyValidator());
//         $this->addValidator('amount', new DoubleNumberValidator());
    }
    
    
    
    
}

