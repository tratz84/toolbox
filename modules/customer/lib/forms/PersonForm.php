<?php

namespace customer\forms;

use customer\form\SelectCompanyListEdit;
use customer\model\AddressDAO;
use customer\model\EmailDAO;
use customer\model\PersonAddressDAO;
use customer\model\PersonDAO;
use customer\model\PersonEmailDAO;
use customer\model\PersonPhoneDAO;
use customer\model\PhoneDAO;
use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\HtmlDatetimeField;
use core\forms\ListFormWidget;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;
use core\service\FormDbMapper;
use customer\model\CompanyPersonDAO;
use customer\model\CompanyDAO;

class PersonForm extends BaseForm {
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('person_id');
        
        hook_htmlscriptloader_enableGroup('iban');
        $this->addJavascript('person-form', '/js/forms/PersonForm.js');
        
        $this->addWidget( new HiddenField('person_id', '', 'Id') );
        
        $this->addWidget( new TextField('firstname', '', t('Firstname')) );
        $this->addWidget( new TextField('insert_lastname', '', t('Middle name')) );
        $this->addWidget( new TextField('lastname', '', t('Lastname')) );
        $this->addWidget( new TextField('iban', '', 'IBAN') );
        $this->addWidget( new TextField('bic', '', 'BIC') );
        
        
        $this->addWidget( new HtmlDatetimeField('edited', '', t('Last modified'), array('hide-when-invalid' => true) ));
        $this->addWidget( new HtmlDatetimeField('created', '', t('Created on'), array('hide-when-invalid' => true) ));
        
        $this->addWidget( new TextareaField('note', '', t('Note')) );
        
        
        $addressList = new ListFormWidget('\\customer\\forms\\AddressForm', 'addressList');
        $addressList->setSortable(true);
        $addressList->setLabel(t('Addresses'));
        $addressList->setFieldLabels(array(t('Street'), t('Housenr'), t('Zipcode'), t('Hometown')));
        $addressList->setFields(array('street', 'street_no', 'zipcode', 'city'));
        $addressList->setPublicFields(array('person_address_id', 'address_id', 'note', 'country_id'));
        $this->addWidget($addressList);
        
        
        $emailList = new ListFormWidget('\\customer\\forms\\EmailForm', 'emailList');
        $emailList->setSortable(true);
        $emailList->setLabel(t('Emailaddresses'));
        $emailList->setFieldLabels(array(t('Email'), t('Description')));
        $emailList->setFields(array('email_address', 'note'));
        $emailList->setPublicFields(array('email_id', 'person_email_id'));
        $this->addWidget($emailList);
        

        $phoneList = new ListFormWidget('\\customer\\forms\\PhoneForm', 'phoneList');
        $phoneList->setSortable(true);
        $phoneList->setLabel(t('Phonenumbers'));
        $phoneList->setFieldLabels(array(t('Phonenumber'), t('Note')));
        $phoneList->setFields(array('phonenr', 'note'));
        $phoneList->setPublicFields(array('phone_id', 'person_phone_id'));
        $this->addWidget($phoneList);
        
        $companyList = new SelectCompanyListEdit('companyList');
        $this->addWidget($companyList);
        
        
        $this->addValidator('lastname', new NotEmptyValidator());
    }
    
    
    public static function getDbMapper() {
        $fdm = new FormDbMapper( self::class, PersonDAO::class );
        $fdm->setLogRefObject('customer__person');
        $fdm->setLogCreatedCode('person-created');
        $fdm->setLogCreatedText('Persoon aangemaakt');
        $fdm->setLogUpdatedCode('person-edited');
        $fdm->setLogUpdatedText('Persoon aangepast');
        $fdm->setLogDeletedCode('person-deleted');
        $fdm->setLogDeletedText('Persoon verwijderd');
        
        $fdm->addPublicField('fullname');
        
        $fdm->addMTON(PersonAddressDAO::class, AddressDAO::class, 'addressList');
        $fdm->addMTON(PersonEmailDAO::class,   EmailDAO::class,   'emailList');
        $fdm->addMTON(PersonPhoneDAO::class,   PhoneDAO::class,   'phoneList');
        $fdm->addMTON(CompanyPersonDAO::class, CompanyDAO::class,  'companyList');
        
        return $fdm;
    }
    
}

