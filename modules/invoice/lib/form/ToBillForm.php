<?php

namespace invoice\form;



use base\service\CompanyService;
use base\service\PersonService;
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

class ToBillForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('to_bill_id');
        
        $this->addWidget(new HiddenField('to_bill_id'));
        $this->addWidget(new CheckboxField('paid', '', t('Paid')));
        $this->addWidget(new SelectField('type', '', ['' => t('Make your choice'), 'bill' => t('Bill'), 'invoice' => t('Invoice')], t('Type')));
        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=base&c=customer&a=select2', 'Klant') );
        
        $this->addWidget(new TextField('short_description', '', 'Korte omschrijving'));
        $this->addWidget(new DoubleField('amount', '', 'Aantal'));
        $this->addWidget(new EuroField('price', '', 'Bedrag'));
        
        $this->addWidget(new TextareaField('long_description', '', 'Notitie'));
        
        $this->addValidator('type', new NotEmptyValidator());
        $this->addValidator('customer_id', new NotEmptyValidator());
        $this->addValidator('short_description', new NotEmptyValidator());
//         $this->addValidator('amount', new DoubleNumberValidator());
    }
    
    
    public function bind($obj) {
        parent::bind($obj);
        
        $companyId = null;
        $personId = null;
        
        $customerWidget = $this->getWidget('customer_id');
        
        if (is_a($obj, ToBill::class)) {
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
    
    public function fill($obj, $fields=array()) {
        parent::fill($obj, $fields);
        
        if (is_a($obj, ToBill::class)) {
            $v = $this->getWidget('customer_id')->getValue();
            $obj->setCompanyId(null);
            $obj->setPersonId(null);
            
            if (strpos($v, 'company-') === 0) {
                $obj->setCompanyId( str_replace('company-', '', $v) );
            }
            
            if (strpos($v, 'person-') === 0) {
                $obj->setPersonId( str_replace('person-', '', $v) );
            }
        }
    }
    
    
    
}

