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
        
        $this->addWidget(new UploadFilesField('files', '', t('Files')) );
        
        $this->addWidget(new HtmlField('statusAsText', '', 'Status'));
        
        if (ctx()->isModuleEnabled('customer')) {
            $this->addWidget( new \customer\forms\CustomerSelectWidget() );
        }
        
        $this->addWidget(new EmailRecipientLineWidget('recipients'));
        
        
        $this->addWidget(new TextField('subject', '', t('Subject')));
        
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
                return t('Recipient - No valid recipients');
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
                $f = new HtmlField('identity_name_email', $obj->getFromName() . ' <'.$obj->getFromEmail().'>', t('From'));
                $f->setPrio(35);
                $this->addWidget( $f );
            }
        }
        
        parent::bind($obj);
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
        $sel = new SelectField('identity_id', '', $options, t('From'));
        $sel->setPrio(35);
        $this->addWidget( $sel );
        
        $this->addValidator('identity_id', new NotZeroValidator());
    }
    
}

