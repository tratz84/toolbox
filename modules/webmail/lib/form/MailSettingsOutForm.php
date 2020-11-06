<?php

namespace webmail\form;

use core\forms\BaseForm;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\validator\NumberValidator;
use core\forms\HiddenField;
use core\forms\CheckboxField;

class MailSettingsOutForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new HiddenField('send_test'));
        
        $this->addWidget(new SelectField('server_type', '', array('local' => 'Local', 'smtp' => 'SMTP Server'), t('Servertype')));
        
        $this->addWidget(new TextField('mail_hostname', '', t('Hostname')));
        $this->addWidget(new TextField('mail_port', '25', t('Port')));
        $this->addWidget(new CheckboxField('mail_tls', '0', t('TLS')));
        
        $this->addWidget(new TextField('mail_username', '', t('Username')));
        $this->addWidget(new TextField('mail_password', '', t('Password')));
        $this->getWidget('mail_password')->disableAutocomplete();
        
        
        $this->addValidator('mail_port', function($form) {
            $st = $form->getWidgetValue('server_type');
            
            if ($st == 'local') {
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
