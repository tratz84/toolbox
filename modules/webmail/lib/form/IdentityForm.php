<?php


namespace webmail\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\EmailField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\validator\EmailValidator;
use core\forms\validator\NotEmptyValidator;
use webmail\service\ConnectorService;
use core\forms\DynamicSelectField;
use core\forms\SelectField;

class IdentityForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('identity_id');
        
        $this->addWidget( new HiddenField('identity_id', '', 'Id') );
        
        $this->addWidget( new CheckboxField('active', '', t('Active')));
        $this->addWidget( new CheckboxField('system_messages', '', t('System messages')));
        $this->getWidget('system_messages')->setInfoText( t('Use this identity for system-messages? ie. password-reset mails') );
        $this->addWidget( new TextField('from_name', '', t('Name')) );
        $this->addWidget( new EmailField('from_email', '', t('E-mail')));
        
        if (ctx()->isExperimental()) {
            $this->addLinkedConnectorId();
        }
        
        
        $this->addValidator('from_name', new NotEmptyValidator());
        $this->addValidator('from_email', new EmailValidator());
        
    }
    
    
    protected function addLinkedConnectorId() {
        
        $map = array();
        $map[''] = t('Make your choice');
        
        /** @var ConnectorService $connectorService */
        $connectorService = object_container_get(ConnectorService::class);
        
        $cons = $connectorService->readConnectors();
        foreach($cons as $c) {
            $map[$c->getConnectorId()] = $c->getDescription();
        }
        
        $sf = new SelectField('connector_id', '', $map, t('Sent mail connector'));
        $sf->setInfoText( t('If connector is an IMAP instance, sent mail is saved in the Sent-folder (if set)') );
        $this->addWidget($sf);
        
    }
    
    
}