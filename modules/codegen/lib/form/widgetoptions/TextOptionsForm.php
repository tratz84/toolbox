<?php


namespace codegen\form\widgetoptions;



use core\forms\CheckboxField;

class TextOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $hwi = new CheckboxField('hide-when-empty', '', 'Hide when empty');
        $this->addWidget($hwi);
    }
    
    
    public function generateExtraSetters($varname) {
        $code = '';
        
        if ($this->getWidgetValue('hide-when-empty')) {
            $code .= $varname . '->setOption( \'hide-when-empty\', true );' . PHP_EOL;
        }
        
        return $code;
    }
    
    
}
