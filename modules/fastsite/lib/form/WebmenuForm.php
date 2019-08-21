<?php


namespace fastsite\form;

use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\SelectImageTextField;
use core\forms\TextField;

class WebmenuForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addWidget(new HiddenField('webmenu_id'));
        $this->addWidget(new TextField('code', '', 'Code'));
        $this->addWidget(new TextField('label', '', 'Label'));
        
        
    }
    
    
}

