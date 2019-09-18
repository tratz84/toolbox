<?php

namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\CheckboxField;
use core\forms\HtmlField;

class TemplateSettingsForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new HiddenField('template_id'));
        $this->addWidget(new HtmlField('template_name', '', 'Naam'));
        $this->addWidget(new CheckboxField('active', '', 'Actief'));
        $this->getWidget('active')->setInfoText('Template gebruiken voor site?');
    }
    
}
