<?php


namespace filesync\form;


use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\TextareaField;

class PagequeueEditForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->enctypeToMultipartFormdata();
        
        $this->addWidget(new HiddenField('pagequeue_id'));
        
        $this->addWidget(new HiddenField('crop_x1'));
        $this->addWidget(new HiddenField('crop_y1'));
        $this->addWidget(new HiddenField('crop_x2', '100'));
        $this->addWidget(new HiddenField('crop_y2', '100'));
        $this->addWidget(new HiddenField('degrees_rotated', '0'));
        $this->addWidget(new HiddenField('page_orientation', 'P'));
        
        $this->addWidget(new TextField('name', '', 'Naam'));
        $this->addWidget(new TextareaField('description', '', 'Omschrijving'));
        
    }
    
    
}
