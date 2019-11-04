<?php


namespace codegen\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\ColorPickerField;
use core\forms\SelectField;
use core\forms\TextField;

class FormGeneratorForm extends BaseForm {
    
    protected $formWidgets;
    
    public function __construct() {
        parent::__construct();
        
        $this->initFormWidgets();
        
        hook_htmlscriptloader_enableGroup('jstree');
        
        
        $this->addModuleSelection();
        $this->addWidget(new TextField('form_name', '', 'Form name'));
    }
    
    protected function initFormWidgets() {
        
        $this->formWidgets = array();
        
        $this->formWidgets[] = array(
            'class' => TextField::class,
            'label' => 'Textfield'
        );
        $this->formWidgets[] = array(
            'class' => CheckboxField::class,
            'label' => 'Checkbox'
        );
        $this->formWidgets[] = array(
            'class' => SelectField::class,
            'label' => 'Select'
        );
        $this->formWidgets[] = array(
            'class' => ColorPickerField::class,
            'label' => 'Color picker'
        );
        
        
        $this->formWidgets = apply_filter('form-generator-form-widgets', $this->formWidgets);
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
