<?php

namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\FileField;

class MediaFileForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget(new FileField('file', '', 'Bestand'));
        
        
        $this->addValidator('file', function($form) {
            
            if (isset($_FILES['file']) == false) {
                return 'No file selected';
            }
            
            if (isset($_FILES['file']['error']) && $_FILES['file']['error']) {
                return 'Error uploading file ('.$_FILES['file']['error'].')';
            }
            
            if (!$_FILES['file']['tmp_name'] || $_FILES['file']['size'] <= 0) {
                return 'Error uploading file (file empty)';
            }
            
        });
        
    }
    
}
