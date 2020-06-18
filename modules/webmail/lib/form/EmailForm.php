<?php

namespace webmail\form;

use customer\service\CompanyService;
use customer\service\PersonService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\DynamicSelectField;
use core\forms\HtmlField;
use core\forms\InternalField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TinymceField;
use core\forms\UploadFilesField;
use core\forms\validator\NotEmptyValidator;
use core\forms\validator\NotZeroValidator;
use invoice\model\Invoice;
use webmail\model\Email;
use webmail\service\EmailService;
use core\forms\HiddenField;

class EmailForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('email_id');
        
        $this->enctypeToMultipartFormdata();
        
        // used for new e-mails
        $this->addWidget(new InternalField('status'));
        $this->addWidget(new InternalField('incoming'));
        $this->addWidget(new HiddenField('solr_mail_id'));
        
        
        
        $this->addWidget(new HtmlField('email_id', '', 'Id'));
        
        $this->addWidget(new UploadFilesField('files', '', 'Bestanden') );
        
        $this->addWidget(new HtmlField('statusAsText', '', 'Status'));
        
        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=customer&c=customer&a=select2', 'Klant') );
        
        $this->addWidget(new EmailRecipientLineWidget('recipients'));
        
        
        $this->addWidget(new TextField('subject', '', 'Onderwerp'));
        
        $this->addWidget(new TinymceField('text_content', '', ''));
        
        
        $this->addValidator('subject', new NotEmptyValidator());
        $this->addValidator('recipients', function($form) {
            $emailRecipientLineWidget = $form->getWidget('recipients');
            
            $objs = $emailRecipientLineWidget->getObjects();
            $validEmailAddresses = 0;
            foreach($objs as $o) {
                if (validate_email($o['to_email']))
                    $validEmailAddresses++;
            }
            
            if ($validEmailAddresses == 0) {
                return 'Geadresseerde - Geen geldige ontvangers';
            } else {
                return null;
            }
        });
        
    }
    
    public function bind($obj) {
        if (is_a($obj, Email::class) && $this->getWidget('identity_id') == false && $this->getWidget('identity_name_email') == false) {
            if ($obj->getStatus() == Email::STATUS_DRAFT) {
                $this->addIdentities();
            } else {
                $f = new HtmlField('identity_name_email', $obj->getFromName() . ' <'.$obj->getFromEmail().'>', 'Van');
                $f->setPrio(35);
                $this->addWidget( $f );
            }
        }
        
        parent::bind($obj);
        
        $companyId = null;
        $personId = null;
        
        if (is_a($obj, Email::class)) {
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
        
        $customerWidget = $this->getWidget('customer_id');
        
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
        
        if (is_a($obj, Email::class)) {
            $v = $this->getWidget('customer_id')->getValue();
            $obj->setCompanyId(0);
            $obj->setPersonId(0);
            
            if (strpos($v, 'company-') === 0) {
                $obj->setCompanyId( str_replace('company-', '', $v) );
            }
            
            if (strpos($v, 'person-') === 0) {
                $obj->setPersonId( str_replace('person-', '', $v) );
            }
        }
    }
    
    
    public function addIdentities() {
        // identity already set? => skip
        if ($this->getWidget('identity_id')) {
            return;
        }
        
        $emailService = ObjectContainer::getInstance()->get(EmailService::class);
        
        $identities = $emailService->readActiveIdentities();
        $options = array();
        foreach($identities as $i) {
            $options[$i->getIdentityId()] = $i->getFromName() . ' <' . $i->getFromEmail() . '>';
        }
        $sel = new SelectField('identity_id', '', $options, 'Van');
        $sel->setPrio(35);
        $this->addWidget( $sel );
        
        $this->addValidator('identity_id', new NotZeroValidator());
    }
    
}

