<?php


namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\TextField;
use core\forms\CheckboxField;
use core\forms\TinymceField;

class WebformForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new CheckboxField('active', '', 'Actief'));
        $this->addWidget(new TextField('webform_name', '', 'Formulier naam'));
        $this->addWidget(new TextField('webform_code', '', 'Formulier code'));
        
        $this->addWidget(new TinymceField('confirmation_message', '', 'Bevestigingsbericht'));
        
    }
    
}
