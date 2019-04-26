<?php

namespace base\forms;

use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\HtmlDatetimeField;
use core\forms\ListFormWidget;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;

class PersonForm extends BaseForm {
    
    
    
    public function __construct() {
        
        
        $this->addKeyField('person_id');
        
        hook_htmlscriptloader_enableGroup('iban');
        $this->addJavascript('person-form', '/js/forms/PersonForm.js');
        
        $this->addWidget( new HiddenField('person_id', '', 'Id') );
        
        $this->addWidget( new TextField('firstname', '', 'Voornaam') );
        $this->addWidget( new TextField('insert_lastname', '', 'Tussenvoegsel') );
        $this->addWidget( new TextField('lastname', '', 'Achternaam') );
        $this->addWidget( new TextField('iban', '', 'IBAN') );
        $this->addWidget( new TextField('bic', '', 'BIC') );
        
        
        $this->addWidget( new HtmlDatetimeField('edited', '', 'Laatst bewerkt', array('hide-when-invalid' => true) ));
        $this->addWidget( new HtmlDatetimeField('created', '', 'Aangemaakt op', array('hide-when-invalid' => true) ));
        
        $this->addWidget( new TextareaField('note', '', 'Notitie') );
        
        
        $addressList = new ListFormWidget('\\base\\forms\\AddressForm', 'addressList');
        $addressList->setSortable(true);
        $addressList->setLabel('Adressen');
        $addressList->setFieldLabels(array('Straat', 'Huisnr', 'Postcode', 'Woonplaats'));
        $addressList->setFields(array('street', 'street_no', 'zipcode', 'city'));
        $addressList->setPublicFields(array('person_address_id', 'address_id', 'note', 'country_id'));
        $this->addWidget($addressList);
        
        
        $emailList = new ListFormWidget('\\base\\forms\\EmailForm', 'emailList');
        $emailList->setSortable(true);
        $emailList->setLabel('E-mailadressen');
        $emailList->setFieldLabels(array('E-mail', 'Omschrijving'));
        $emailList->setFields(array('email_address', 'note'));
        $emailList->setPublicFields(array('email_id', 'person_email_id'));
        $this->addWidget($emailList);
        

        $phoneList = new ListFormWidget('\\base\\forms\\PhoneForm', 'phoneList');
        $phoneList->setSortable(true);
        $phoneList->setLabel('Telefoonnummers');
        $phoneList->setFieldLabels(array('Telefoonnummer', 'Notitie'));
        $phoneList->setFields(array('phonenr', 'note'));
        $phoneList->setPublicFields(array('phone_id', 'person_phone_id'));
        $this->addWidget($phoneList);
        
        
        $this->addValidator('lastname', new NotEmptyValidator());
    }
    
}

