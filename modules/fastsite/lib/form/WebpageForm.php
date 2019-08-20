<?php

namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\TextField;
use core\forms\SelectField;
use core\forms\TextareaField;
use core\forms\CheckboxField;

class WebpageForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addWidget(new CheckboxField('active', null, 'Active'));
        $this->addWidget(new TextField('code', '', 'Code'));
        $this->addWidget(new SelectField('module', '', array('' => 'Default'), 'Module'));
        $this->addWidget(new TextField('url', '', 'Url'));
        
        $this->addWidget(new TextField('meta_title',       '', 'Titel'));
        $this->addWidget(new TextField('meta_description', '', 'Meta description'));
        $this->addWidget(new TextField('meta_keywords',    '', 'Meta keywords'));
        $this->addWidget(new TextareaField('content1', '', 'Content 1'));
        $this->addWidget(new TextareaField('content2', '', 'Content 2'));
        
        
    }
    
    
}