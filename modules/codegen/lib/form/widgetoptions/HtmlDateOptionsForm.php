<?php


namespace codegen\form\widgetoptions;



use core\forms\CheckboxField;

class HtmlDateOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $hwi = new CheckboxField('hide-when-invalid', '', 'Hide when invalid');
        $this->addWidget($hwi);
    }
    
    
    public function generateExtraSetters($varname) {
        $code = '';
        
        if ($this->getWidgetValue('hide-when-invalid')) {
            $code .= $varname . '->setOption( \'hide-when-invalid\', true );' . PHP_EOL;
        }
        
        return $code;
    }
    
    
}
