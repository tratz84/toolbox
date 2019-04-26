<?php

namespace signrequest\form;


use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\SelectField;
use core\forms\TinymceField;
use core\forms\UploadFilesField;
use core\forms\validator\NotEmptyValidator;
use core\forms\validator\NotZeroValidator;
use webmail\service\EmailService;

class SignRequestForm extends BaseForm {
    
    public function __construct() {
        
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget( new HiddenField('message_id', '', 'Id') );
        $this->addWidget( new HiddenField('offer_id', '', 'Id') );
        $this->addWidget( new HiddenField('ref_object', '', 'Ref obj') );
        $this->addWidget( new HiddenField('ref_id', '', 'Ref id') );
        
        $this->addWidget(new UploadFilesField('files', array(), 'Bestanden') );
        
        $this->addIdentities();
        
        $this->addWidget( new SignerEmailLineWidget('signers') );
        
        $this->addWidget(new TinymceField('message', '', ''));
        
        $this->addValidator('identity_id', new NotEmptyValidator());
        $this->addValidator('signer_email', function($form) {
            $signers = $form->getWidget('signers');
            
            $objs = $signers->getObjects();
            $validEmailAddresses = 0;
            foreach($objs as $o) {
                if (validate_email($o['signer_email']))
                    $validEmailAddresses++;
            }
            
            if ($validEmailAddresses == 0) {
                return 'Geadresseerde - Geen geldige ontvangers';
            } else {
                return null;
            }
        });
    }
    
    
    protected function addIdentities() {
        $emailService = ObjectContainer::getInstance()->get(EmailService::class);
        
        $identities = $emailService->readActiveIdentities();
        $options = array();
        foreach($identities as $i) {
            $options[$i->getIdentityId()] = $i->getFromName() . ' <' . $i->getFromEmail() . '>';
        }
        $sel = new SelectField('identity_id', '', $options, 'Van');
        $this->addWidget( $sel );
        
        $this->addValidator('identity_id', new NotZeroValidator());
    }
}
