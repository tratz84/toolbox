<?php

namespace payment\form;

use core\forms\BaseForm;
use core\forms\FileField;
use core\forms\SelectField;
use core\forms\validator\NotEmptyValidator;

class PaymentImportForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->enctypeToMultipartFormdata();
        
        $this->setSubmitText('Map fields');
        
        $mapTypes = array();
        $mapTypes['sheet'] = 'CSV / XLS';
        $this->addWidget(new SelectField('filetype', '', $mapTypes, 'Soort bestand'));
        
        
        $this->addWidget(new FileField('file', '', 'Bestand'));
        
        
        
        
        $this->addValidator('filetype', new NotEmptyValidator());
        
        
        $this->addValidator('file', function($form) {
            if (isset($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name']) && filesize($_FILES['file']['tmp_name']) > 0) {
            } else {
                return t('Upload failed');
            }
            
            $ext = file_extension($_FILES['file']['name']);
            
            if (in_array($ext, array('csv', 'xls', 'xlsx')) == false) {
                return t('Unknown filetype');
            }
            
        });
        
    }
    
    
    public function isCsv() {
        return file_extension($_FILES['file']['name']) == 'csv';
    }
    
    
}


