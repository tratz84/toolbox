<?php

namespace project\form;

use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;

class ProjectHourTypeForm extends BaseForm {
    
    
    public function __construct() {
        
        $this->addKeyField('project_hour_type_id');
        
        $this->addWidget(new HiddenField('project_hour_type_id'));
        
        
        $this->addWidget(new CheckboxField('default_selected', '', 'Standaard gekozen'));
        $this->addWidget(new CheckboxField('visible', '', 'Zichtbaar'));
        $this->addWidget(new TextField('description', '', 'Omschrijving'));
        
    }
    
    
}

