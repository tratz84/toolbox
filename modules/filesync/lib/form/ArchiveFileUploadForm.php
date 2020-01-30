<?php


namespace filesync\form;


use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\DynamicSelectField;
use core\forms\FileField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\DateValidator;

class ArchiveFileUploadForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget(new HiddenField('store_id'));
        $this->addWidget(new FileField('file', '', 'Bestand'));
        $this->addWidget(new DatePickerField('document_date', '', 'Document datum'));
        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=base&c=customer&a=select2', 'Klant') );
        $this->addWidget(new TextField('subject', '', 'Onderwerp'));
        $this->addWidget(new TextareaField('long_description', '', 'Lange omschrijving'));
        
        
        // validate upload
        $this->addValidator('file', function($form) {
            if (isset($_FILES['file']) == false || $_FILES['file']['size'] <= 0) {
                if (isset($_FILES['file']) && isset($_FILES['file']['error'])) {
                    if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
                        return 'Bestand te groot (vraag admin dit op te lossen)';
                    }
                    
                    if ($_FILES['file']['error'] == UPLOAD_ERR_NO_TMP_DIR) {
                        return 'Geen tmp-folder (vraag admin dit op te lossen)';
                    }
                    if ($_FILES['file']['error'] == UPLOAD_ERR_CANT_WRITE) {
                        return 'Geen schrijf-permissies (vraag admin dit op te lossen)';
                    }
                }
                
                return 'Geen bestand gekozen';
            }
        });
        
        $this->addValidator('document_date', new DateValidator());
        
    }
    
    
}
