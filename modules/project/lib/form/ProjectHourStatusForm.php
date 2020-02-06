<?php

namespace project\form;

use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;

class ProjectHourStatusForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('project_hour_status_id');
        
        $this->addWidget(new HiddenField('project_hour_status_id'));
        
        
        $this->addWidget(new CheckboxField('default_selected', '', 'Standaard gekozen'));
        $this->addWidget(new TextField('description', '', 'Omschrijving'));
        
    }
    
    
}

