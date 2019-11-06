<?php

namespace codegen\form\widgetoptions;


use core\forms\BaseForm;
use core\forms\HtmlField;
use core\forms\TextField;

class DefaultWidgetOptionsForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->disableSubmit();
        
        $this->addWidget(new HtmlField('class', '', ''));
        
        $this->addWidget(new TextField('name', '', 'Name'));
        $this->addWidget(new TextField('label', '', 'Label'));
        $this->addWidget(new TextField('defaultValue', '', 'Default value'));
    }
    
}
