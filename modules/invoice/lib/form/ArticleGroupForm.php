<?php

namespace invoice\form;

use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;

class ArticleGroupForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget( new HiddenField('article_group_id', '', ''));
        
        $this->addWidget( new CheckboxField('active',            '', 'Actief') );
        $this->addWidget( new TextField('group_name',            '', 'Naam') );
        $this->addWidget( new TextareaField('long_description1', '', 'Omschrijving 1') );
        $this->addWidget( new TextareaField('long_description2', '', 'Omschrijving 2') );
        
        
        $this->addValidator('group_name', new NotEmptyValidator());
        
    }
    
    
}
