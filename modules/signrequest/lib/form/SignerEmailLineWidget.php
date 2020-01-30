<?php


namespace signrequest\form;

use core\forms\EmailField;
use core\forms\HiddenField;
use core\forms\ListEditWidget;

class SignerEmailLineWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        parent::__construct($methodObjectList);
        
        $this->init();
        
        $this->tableHeader = false;
        $this->strNewEntry = '<span class="fa fa-plus"></span> Geadresseerde toevoegen';
        $this->sortable = false;
    }
    
    protected function init() {
        $this->addWidget( new HiddenField('message_signer_id', '', 'Id') );
        $this->addWidget( new HiddenField('message_id', '', 'E-mail id') );
        
        $this->addWidget( new EmailField('signer_email', '', 'E-mailadres') );
        $this->getWidget('signer_email')->showPlaceholder();
    }
    
}

