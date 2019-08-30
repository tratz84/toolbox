<?php


namespace filesync\form;


use core\forms\BaseForm;
use core\forms\FileField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\TextareaField;

class PagequeueUploadForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget(new HiddenField('pagequeue_id'));
        
        $ff = new FileField('file', '', 'Bestand');
        $ff->setAttribute('accept', 'image/*');
        $ff->setAttribute('capture', 'capture');
        $this->addWidget($ff);
        
        $this->addWidget(new HiddenField('crop_x1'));
        $this->addWidget(new HiddenField('crop_y1'));
        $this->addWidget(new HiddenField('crop_x2', '100'));
        $this->addWidget(new HiddenField('crop_y2', '100'));
        $this->addWidget(new HiddenField('degrees_rotated', '0'));
        $this->addWidget(new HiddenField('page_orientation', 'P'));
        
        $this->addWidget(new TextField('name', '', 'Naam'));
        $this->addWidget(new TextareaField('description', '', 'Omschrijving'));
        
        
        
        $this->addValidator('file', function($form) {
            $pagequeueId = $form->getWidgetValue('pagequeue_id');
            
            // just changing name/description ?
            if ($pagequeueId && (isset($_FILES['file']) == false || $_FILES['file']['error'] == UPLOAD_ERR_NO_FILE)) {
                return null;
            }
            
            // validate upload
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
        
    }
    
    
}
