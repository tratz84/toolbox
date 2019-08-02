<?php

namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\FileField;

class WebsiteTemplateForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget(new FileField('file'));
    }
    
}

