<?php


namespace filesync\form;


use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\FileField;
use core\forms\HiddenField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\DateValidator;
use filesync\service\StoreService;
use customer\forms\CustomerSelectWidget;

class StoreFileUploadForm extends BaseForm {
    
    
    public function __construct($opts=array()) {
        parent::__construct();
        
        $this->enctypeToMultipartFormdata();
        
        if (isset($opts['store_as_list']) && $opts['store_as_list']) {
            $this->addStoreFileList();
        } else {
            $this->addWidget(new HiddenField('store_id'));
        }
        $this->addWidget(new FileField('file', '', t('File')));
        
        $this->addWidget(new TextField('path', '', t('Path')));
        
        $this->addWidget(new DatePickerField('document_date', '', t('Document date')));
        $this->addWidget(new CustomerSelectWidget());
        
        $this->addWidget(new TextField('subject', '', t('Subject')));
        $this->addWidget(new TextareaField('long_description', '', t('Long description')));
        
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
        
        
        $this->addValidator('path', function($form) {
            $path = $form->getWidgetValue('path');
            $path = str_replace('\\', '/', $path);
            
            $tokens = explode('/', $path);
            
            foreach($tokens as $t) {
                if (strpbrk($t, "\\/?%*:|\"<>") !== false) {
                    return 'Invalid path';
                }
            }
        });
        
//         $this->addValidator('document_date', new DateValidator());
        
        $this->hideSubmitButtons();
    }
    
    
    
    
    protected function addStoreFileList() {
        $storeService = object_container_get(StoreService::class);
        $stores = $storeService->readAllStores();
        
        $map = array();
        
        foreach($stores as $s) {
            if ($s->getStoreType() == 'share') {
                $map[$s->getStoreId()] = $s->getStoreName();
            }
        }
        
        $this->addWidget(new SelectField('store_id', '', $map, 'Store'));
    }
    
    
}

