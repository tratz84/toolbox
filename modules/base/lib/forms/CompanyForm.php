<?php

namespace base\forms;

use base\service\CompanyService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\HtmlDatetimeField;
use core\forms\ListFormWidget;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;
use core\forms\HtmlField;

class CompanyForm extends BaseForm {
    
    
    
    public function __construct() {
        
        $this->addKeyField('company_id');
        
        hook_htmlscriptloader_enableGroup('iban');
        $this->addJavascript('company-form', '/js/forms/CompanyForm.js');
        
        $this->addWidget( new HiddenField('company_id', '', 'Id') );
        
//         $this->addCompanyTypes();
        
        $this->addWidget( new TextField('company_name', '', 'Bedrijfsnaam') );
        $this->addWidget( new TextField('contact_person', '', 'Contactpersoon') );
        $this->addWidget( new TextField('coc_number', '', 'Kvk nummer') );
        $this->addWidget( new TextField('vat_number', '', 'Btw nummer') );
        $this->addWidget( new TextField('iban', '', 'IBAN') );
        $this->addWidget( new TextField('bic', '', 'BIC') );
        
        
        $this->addWidget( new HtmlDatetimeField('edited', '', 'Laatst bewerkt', array('hide-when-invalid' => true) ));
        $this->addWidget( new HtmlDatetimeField('created', '', 'Aangemaakt op', array('hide-when-invalid' => true) ));
        
        $this->addWidget( new TextareaField('note', '', 'Notitie') );
        
        $addressList = new ListFormWidget('\\base\\forms\\AddressForm', 'addressList');
        $addressList->setSortable(true);
        $addressList->setLabel('Adressen');
        $addressList->setFieldLabels(array('Straat', 'Huisnr', 'Postcode', 'Plaats'));
        $addressList->setFields(array('street', 'street_no', 'zipcode', 'city'));
        $addressList->setPublicFields(array('company_address_id', 'address_id', 'note', 'country_id'));
        $this->addWidget($addressList);
        
        
        $emailList = new ListFormWidget('\\base\\forms\\EmailForm', 'emailList');
        $emailList->setSortable(true);
        $emailList->setLabel('E-mailadressen');
        $emailList->setFieldLabels(array('E-mailadres', 'Omschrijving'));
        $emailList->setFields(array('email_address', 'note'));
        $emailList->setPublicFields(array('email_id', 'company_email_id', 'primary_address'));
        $this->addWidget($emailList);
        

        $phoneList = new ListFormWidget('\\base\\forms\\PhoneForm', 'phoneList');
        $phoneList->setSortable(true);
        $phoneList->setLabel('Telefoonnummers');
        $phoneList->setFieldLabels(array('Telefoonnummer', 'Notitie'));
        $phoneList->setFields(array('phonenr', 'note'));
        $phoneList->setPublicFields(array('phone_id', 'company_phone_id'));
        $this->addWidget($phoneList);
        
        
        $this->addValidator('company_name', new NotEmptyValidator());
    }
    
    
    protected function addCompanyTypes() {
        
        $defaultSelected = '';
        $options = array();
        
        $companyService = ObjectContainer::getInstance()->get(CompanyService::class);
        $types = $companyService->readTypes();
        
        foreach($types as $t) {
            $options[$t->getCompanyTypeId()] = $t->getTypeName();
        }
        
        $this->addWidget( new SelectField('company_type_id', $defaultSelected, $options, 'Bedrijfssoort'));
        
    }
    
    
}
