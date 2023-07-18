<?php

namespace webmail\form;

use core\forms\BaseForm;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\validator\NumberValidator;
use core\forms\HiddenField;
use core\forms\CheckboxField;
use core\forms\WidgetContainer;
use webmail\service\CloudTokenService;
use core\forms\validator\NotEmptyValidator;

class MailSettingsOutForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new HiddenField('send_test'));
        
        $serverTypes = array();
        $serverTypes['local'] = 'Local';
        $serverTypes['smtp']  = 'SMTP Server';
        
        if (ctx()->isExperimental()) {
            $serverTypes['azure'] = 'Office 365 / Azure';
        }
        
        $this->addWidget(new SelectField('server_type', '', $serverTypes, t('Servertype')));

        // smtp settings
        $smtpSettings = new WidgetContainer( 'smtp-settings' );
        
        $smtpSettings->addWidget(new TextField('mail_hostname', '', t('Hostname')));
        $smtpSettings->addWidget(new TextField('mail_port', '25', t('Port')));
        $smtpSettings->addWidget(new CheckboxField('mail_tls', '0', t('TLS')));
        
        $smtpSettings->addWidget(new TextField('mail_username', '', t('Username')));
        $smtpSettings->addWidget(new TextField('mail_password', '', t('Password')));
        $smtpSettings->getWidget('mail_password')->disableAutocomplete();
        $this->addWidget( $smtpSettings);
        
        // azure token stuff
        if (ctx()->isExperimental()) {
            $ctService = object_container_get( CloudTokenService::class );
            $azureTokens = $ctService->readAzureTokens();
            $mapTokens = array();
            $mapTokens[''] = t('Make your choice');
            foreach($azureTokens as $at) {
                $mapTokens[ $at->getWebmailAzureTokenId() ] = $at->getDescription();
            }
            
            $azureSettings = new WidgetContainer( 'azure-settings' );
            $azureTokenId = new SelectField( 'azure_token_id', null, $mapTokens, 'Azure token' );
            $azureSettings->addWidget( $azureTokenId );
            
            $this->addWidget( $azureSettings );
            
            $this->addValidator( 'azure_token_id', new NotEmptyValidator() );
        }
        
        
        
        $this->addValidator('mail_port', function($form) {
            $st = $form->getWidgetValue('server_type');
            
            if ($st != 'smtp') {
                return;
            }
            
            // validate port nr
            $w = $form->getWidget('mail_port');
            $v = new NumberValidator(array('empty-allowed' => true, 'min' => 1, 'max' => 65535));
            if ($v->validate($w) == false) {
                return $v->getMessage();
            }
        });
        
    }
    
}
