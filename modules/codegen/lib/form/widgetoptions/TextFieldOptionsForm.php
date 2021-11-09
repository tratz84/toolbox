<?php


namespace codegen\form\widgetoptions;



use core\forms\TextField;

class TextFieldOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $tfp = new TextField('placeholder', null, 'Placeholder');
        $this->addWidget($tfp);
    }
    
    
    public function generateExtraSetters($varname) {
        $code = '';
        
        if ($this->getWidgetValue('placeholder')) {
            $v = var_export( $this->getWidgetValue('placeholder'), true );
            $code .= $varname . '->setPlaceholder( '.$v.' );' . PHP_EOL;
        }
        
        return $code;
    }
    
    
}


