<?php

namespace filesync\form;



use customer\service\CompanyService;
use customer\service\PersonService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\DynamicSelectField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\TextField;
use core\forms\TextareaField;
use invoice\model\Offer;
use filesync\model\StoreFileMeta;
use customer\forms\CustomerSelectWidget;

class StoreFileMetaForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('store_file_id');
        
        $this->addWidget(new HiddenField('store_id'));
        $this->addWidget(new HiddenField('store_file_id'));
        $this->addWidget(new HtmlField('filename', '', 'Bestandsnaam'));
        $this->addWidget(new DatePickerField('document_date', '', 'Document datum'));
        $this->addWidget(new CustomerSelectWidget());
        $this->addWidget(new TextField('subject', '', 'Onderwerp'));
        $this->addWidget(new TextareaField('long_description', '', 'Lange omschrijving'));
        
        
    }
    
    
}

