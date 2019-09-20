<?php


namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\TextField;
use core\forms\CheckboxField;
use core\forms\TinymceField;
use core\forms\WidgetContainer;
use core\forms\HtmlField;

class WebformForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new CheckboxField('active', '', 'Actief'));
        $this->addWidget(new TextField('webform_name', '', 'Formulier naam'));
        $this->addWidget(new TextField('webform_code', '', 'Formulier code'));
        
        $this->addWidget(new TinymceField('confirmation_message', '', 'Bevestigingsbericht'));
        $this->getWidget('confirmation_message')->setInfoText('Getoond bericht na versturen formulier');
        
        $this->addWidget(new HtmlField('', '', 'Form fields'));
        $this->addWidget(new WidgetContainer('webform-fields'));
    }
    
}
