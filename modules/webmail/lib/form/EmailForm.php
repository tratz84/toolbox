<?php

namespace webmail\form;

use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\TinymceField;
use webmail\service\EmailService;
use core\forms\SelectField;
use webmail\model\Email;
use core\forms\HtmlField;
use core\forms\UploadFilesField;
use core\forms\validator\NotEmptyValidator;
use core\forms\validator\NotZeroValidator;

class EmailForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('email_id');
        
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget(new HtmlField('email_id', '', 'Id'));
        
        $this->addWidget(new UploadFilesField('files', '', 'Bestanden') );
        
        $this->addWidget(new HtmlField('statusAsText', '', 'Status'));
        
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
            if ($obj->getStatus() == 'draft') {
                $this->addIdentities();
            } else {
                $f = new HtmlField('identity_name_email', $obj->getFromName() . ' <'.$obj->getFromEmail().'>', 'Van');
                $f->setPrio(35);
                $this->addWidget( $f );
            }
        }
        
        parent::bind($obj);
    }
    
    protected function addIdentities() {
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

