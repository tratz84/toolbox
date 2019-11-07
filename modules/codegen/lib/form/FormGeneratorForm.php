<?php


namespace codegen\form;


use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;

class FormGeneratorForm extends BaseForm {
    
    protected $formWidgets;
    
    public function __construct() {
        parent::__construct();
        
        $this->initFormWidgets();
        
        hook_htmlscriptloader_enableGroup('jstree');
        
        $this->addModuleSelection();
        $this->addWidget(new TextField('form_name', '', 'Form name'));
        $this->addWidget(new HiddenField('treedata'));
        
        
        
        $this->addValidator('module_name', new NotEmptyValidator());
    }
    
    
    public function getFormWidgets() {
        return $this->formWidgets;
    }
    
    protected function initFormWidgets() {
        
        $this->formWidgets = \codegen\generator\GeneratorHelper::getWidgets();
        
    }
    
    public function getEditorClass($widgetClass) {
        for($x=0; $x < count($this->formWidgets); $x++) {
            if ($this->formWidgets[$x]['class'] == $widgetClass) {
                if (isset($this->formWidgets[$x]['editor'])) {
                    return $this->formWidgets[$x]['editor'];
                } else {
                    return \codegen\form\widgetoptions\DefaultWidgetOptionsForm::class;
                }
            }
        }
        
        return null;
    }
    
    protected function addModuleSelection() {
        $modules = module_list();
        
        
        $map = array();
        $map[''] = 'Make your choice';
        foreach($modules as $key => $path) {
            $map[$key] = $key;
        }
        
        $this->addWidget(new SelectField('module_name', '', $map, 'Module'));
        
    }
    
    
}
