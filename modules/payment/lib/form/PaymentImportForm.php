<?php

namespace payment\form;

use core\forms\BaseForm;
use core\forms\FileField;

class PaymentImportForm extends BaseForm {
    
    
    public function __construct() {
        
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget(new FileField('file', '', 'Bestand'));
        
        
        $this->addValidator('file', function($form) {
            if (isset($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name']) && filesize($_FILES['file']['tmp_name']) > 0) {
            } else {
                return 'Upload failed';
            }
        });
        
    }
    
    
    public function isCsv() {
        return file_extension($_FILES['file']['name']) == 'csv';
    }
    
    
}


