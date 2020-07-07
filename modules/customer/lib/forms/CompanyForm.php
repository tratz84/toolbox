<?php

namespace customer\forms;

use customer\form\SelectPersonListEdit;
use customer\model\AddressDAO;
use customer\model\CompanyAddressDAO;
use customer\model\CompanyDAO;
use customer\model\CompanyEmailDAO;
use customer\model\CompanyPersonDAO;
use customer\model\CompanyPhoneDAO;
use customer\model\EmailDAO;
use customer\model\PersonDAO;
use customer\model\PhoneDAO;
use customer\service\CompanyService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\HtmlDatetimeField;
use core\forms\ListFormWidget;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;
use core\service\FormDbMapper;

class CompanyForm extends BaseForm {
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('company_id');
        
        hook_htmlscriptloader_enableGroup('iban');
        $this->addJavascript('company-form', '/js/forms/CompanyForm.js');
        
        $this->addWidget( new HiddenField('company_id', '', 'Id') );
        
//         $this->addCompanyTypes();
        
        $this->addWidget( new TextField('company_name', '', t('Companyname')) );
        $this->addWidget( new TextField('contact_person', '', t('Contact person')) );
        $this->addWidget( new TextField('coc_number', '', t('Coc number')) );
        $this->addWidget( new TextField('vat_number', '', t('VAT number')) );
        $this->addWidget( new TextField('iban', '', 'IBAN') );
        $this->addWidget( new TextField('bic', '', 'BIC') );
        
        
        $this->addWidget( new HtmlDatetimeField('edited', '', t('Last modified'), array('hide-when-invalid' => true) ));
        $this->addWidget( new HtmlDatetimeField('created', '', t('Created on'), array('hide-when-invalid' => true) ));
        
        $this->addWidget( new TextareaField('note', '', 'Notitie') );
        
        $addressList = new ListFormWidget('\\customer\\forms\\AddressForm', 'addressList');
        $addressList->setSortable(true);
        $addressList->setLabel('Adressen');
        $addressList->setFieldLabels(array(t('Street'), t('Housenr'), t('Zipcode'), t('City')));
        $addressList->setFields(array('street', 'street_no', 'zipcode', 'city'));
        $addressList->setPublicFields(array('company_address_id', 'address_id', 'note', 'country_id'));
        $this->addWidget($addressList);
        
        
        $emailList = new ListFormWidget('\\customer\\forms\\EmailForm', 'emailList');
        $emailList->setSortable(true);
        $emailList->setLabel('E-mailadressen');
        $emailList->setFieldLabels(array(t('Email address'), t('Description')));
        $emailList->setFields(array('email_address', 'note'));
        $emailList->setPublicFields(array('email_id', 'company_email_id', 'primary_address'));
        $this->addWidget($emailList);
        

        $phoneList = new ListFormWidget('\\customer\\forms\\PhoneForm', 'phoneList');
        $phoneList->setSortable(true);
        $phoneList->setLabel('Telefoonnummers');
        $phoneList->setFieldLabels(array(t('Phone number'), t('Note')));
        $phoneList->setFields(array('phonenr', 'note'));
        $phoneList->setPublicFields(array('phone_id', 'company_phone_id'));
        $this->addWidget($phoneList);
        
        $personList = new SelectPersonListEdit();
        $this->addWidget( $personList );
        
        
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
    
    
    public static function getDbMapper() {
        $fdm = new FormDbMapper( self::class, CompanyDAO::class );
        $fdm->setLogCreatedCode('company-created');
        $fdm->setLogCreatedText('Bedrijf aangemaakt');
        $fdm->setLogUpdatedCode('company-edited');
        $fdm->setLogUpdatedText('Bedrijf aangepast');
        $fdm->setLogDeletedCode('company-deleted');
        $fdm->setLogDeletedText('Bedrijf verwijderd');
        
        $fdm->addMTON(CompanyAddressDAO::class, AddressDAO::class, 'addressList');
        $fdm->addMTON(CompanyEmailDAO::class,   EmailDAO::class,   'emailList');
        $fdm->addMTON(CompanyPhoneDAO::class,   PhoneDAO::class,   'phoneList');
        $fdm->addMTON(CompanyPersonDAO::class,  PersonDAO::class,  'personList');
        
        return $fdm;
    }
    
    
}
