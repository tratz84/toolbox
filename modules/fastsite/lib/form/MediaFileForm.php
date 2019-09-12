<?php

namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\FileField;

class MediaFileform extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new FileField('file', '', 'Bestand'));
        
        
    }
    
}
