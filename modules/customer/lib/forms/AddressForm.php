<?php

namespace customer\forms;

use customer\service\CustomerService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;

class AddressForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addJavascript('address-form', appUrl('?mpf=/module/customer/js/address-form.js'));
        
        $customerService = ObjectContainer::getInstance()->get(CustomerService::class);
        $countries = $customerService->getCountries();
        
        $this->addWidget( new TextField('zipcode',   '', t('Zipcode')) );
        $this->addWidget( new TextField('street_no', '', t('Housenumber')) );
        $this->addWidget( new TextField('street',    '', t('Street')) );
        $this->addWidget( new TextField('city',      '', t('City')) );
        
        $this->addWidget( new SelectField('country_id', '148', $countries, t('Country')) );
        
        
        $this->addWidget( new TextField('note',      '', t('Note')) );
        
        $this->addValidator('street', new NotEmptyValidator());
    }
    
    
}
