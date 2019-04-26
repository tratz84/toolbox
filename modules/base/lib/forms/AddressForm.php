<?php

namespace base\forms;

use base\service\CustomerService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;

class AddressForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $customerService = ObjectContainer::getInstance()->get(CustomerService::class);
        $countries = $customerService->getCountries();
        
        $this->addWidget( new TextField('street',    '', 'Straat') );
        $this->addWidget( new TextField('street_no', '', 'Huisnummer') );
        $this->addWidget( new TextField('zipcode',   '', 'Postcode') );
        $this->addWidget( new TextField('city',      '', 'Plaats') );
        
        $this->addWidget( new SelectField('country_id', '148', $countries, 'Land') );
        
        
        $this->addWidget( new TextField('note',      '', 'Notitie') );
        
        $this->addValidator('street', new NotEmptyValidator());
    }
    
    
}
