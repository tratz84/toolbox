<?php


namespace webmail\form;

use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\EmailField;

class TemplateToLineWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        parent::__construct($methodObjectList);
        
        $this->init();
        
        $this->tableHeader = false;
        $this->strNewEntry = '<span class="fa fa-plus"></span> Geadresseerde toevoegen';
        $this->sortable = false;
    }
    
    protected function init() {
        $this->addWidget( new HiddenField('template_to_id', '', 'Id') );
        $this->addWidget( new HiddenField('template_id', '', 'E-mail id') );
        
        $this->addWidget( new SelectField('to_type', 'to', array('To' => 'Aan', 'Cc' => 'Cc', 'Bcc' => 'Bcc'), 'Aan') );
        
        $this->addWidget( new TextField('to_name', '', 'Naam' ));
        $this->getWidget('to_name')->showPlaceholder();
        
        $this->addWidget( new EmailField('to_email', '', 'E-mailadres') );
        $this->getWidget('to_email')->showPlaceholder();
        
    }
    
    
}
