<?php

namespace filesync\form;



use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\DatePickerField;
use core\forms\FileField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\InternalField;
use core\forms\TextField;
use core\forms\TextareaField;
use customer\forms\CustomerSelectWidget;
use filesync\service\StoreService;

class StoreFileMetaForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('store_file_id');
        
        $this->addWidget(new InternalField('public_secret'));
        $this->addWidget(new HiddenField('store_id'));
        $this->addWidget(new HiddenField('store_file_id'));
        $this->addWidget(new HtmlField('path', '', 'Pad'));
        $this->addWidget(new DatePickerField('document_date', '', 'Document datum'));
        $this->addWidget(new CustomerSelectWidget());
        $this->addWidget(new TextField('subject', '', 'Onderwerp'));
        $this->addWidget(new TextareaField('long_description', '', 'Lange omschrijving'));
        
        $this->addWidget(new CheckboxField('public', '', 'Public'));
        $this->getWidget('public')->setInfoText('Create download link for file?');
    }
    
    
    // add file-field for updating file
    public function addFileWidget() {
        // set form's enctype
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget( new FileField('file', '', t('File')) );
        $this->getWidget('file')->setPrio(1);
        
        // check if 'file' is set and if last file is the same, if yes => handle as error
        $this->addValidator('file', function($form) {
            if (isset($_FILES['file']['size']) == false) {
                return;
            }
            
            $sfid = $form->getWidgetValue('store_file_id');
            if (!$sfid) {
                return;
            }
            
            $storeService = object_container_get( StoreService::class );
            $sf = $storeService->readStoreFile($sfid);
            
            if (md5_file($_FILES['file']['tmp_name']) == $sf->getField('md5sum')) {
                return 'Uploaded file same as last revision';
            }
            
        });
    }
    
    
    public function render() {
        
        if ($this->getWidgetValue('public')) {
            if (!$this->getWidget('public_url')) {
                $this->addWidget(new HtmlField('public_url', '', 'Public url'));
                $this->getWidget('public_url')->setEscapeValue( false );
            }
            
            $sfid = $this->getWidgetValue('store_file_id');
            $s = $this->getWidgetValue('public_secret');
            $u = BASE_URL . appUrl('/?m=filesync&c=public/storefile&a=download&sfid='.$sfid.'&ps='.$s);
            
            $this->getWidget('public_url')->setValue( '<a href="'.esc_attr($u).'" target="_blank">'.esc_html($u).'</a>' );
        }
        
        return parent::render();
    }
    
}

