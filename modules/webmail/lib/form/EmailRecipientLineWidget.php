<?php


namespace webmail\form;

use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\EmailField;

class EmailRecipientLineWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        parent::__construct($methodObjectList);
        
        $this->init();
        
        $this->tableHeader = false;
        $this->strNewEntry = '<span class="fa fa-plus"></span> Geadresseerde toevoegen';
        $this->sortable = false;
    }
    
    protected function init() {
        $this->addWidget( new HiddenField('email_to_id', '', 'Id') );
        $this->addWidget( new HiddenField('email_id', '', 'E-mail id') );
        
        $this->addWidget( new SelectField('to_type', 'to', array('To' => t('To'), 'Cc' => 'Cc', 'Bcc' => 'Bcc'), t('To')) );
        
        $this->addWidget( new TextField('to_name', '', t('Name') ));
        $this->getWidget('to_name')->showPlaceholder();
        
        $this->addWidget( new EmailField('to_email', '', t('Email address')) );
        $this->getWidget('to_email')->showPlaceholder();
        
    }
    
    
    protected function addVatPercentages() {
        
        $this->addWidget( new SelectField('vat', $defaultSelected, $options, 'Btw', array('add-unlisted' => true)));
    }
    
    
    
}
