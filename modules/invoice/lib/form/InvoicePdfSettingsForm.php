<?php


namespace invoice\form;


use core\forms\BaseForm;
use core\forms\ColorPickerField;

class InvoicePdfSettingsForm extends BaseForm {
    
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addWidget(new ColorPickerField('color_frame', '', 'Kleur-frame'));
        $this->addWidget(new ColorPickerField('color_row1', '', 'Kleur rijen even'));
        $this->addWidget(new ColorPickerField('color_row2', '', 'Kleur rijen oneven'));
        
    }
    
    
}
