<?php


namespace codegen\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\ColorPickerField;
use core\forms\SelectField;
use core\forms\TextField;
use codegen\form\widgetoptions\SelectOptionsForm;
use core\forms\WidgetContainer;
use core\forms\HiddenField;

class FormGeneratorForm extends BaseForm {
    
    protected $formWidgets;
    
    public function __construct() {
        parent::__construct();
        
        $this->initFormWidgets();
        
        hook_htmlscriptloader_enableGroup('jstree');
        
        $this->addModuleSelection();
        $this->addWidget(new TextField('form_name', '', 'Form name'));
        $this->addWidget(new HiddenField('treedata'));
    }
    
    
    public function getFormWidgets() {
        return $this->formWidgets;
    }
    
    protected function initFormWidgets() {
        
        $this->formWidgets = array();
        
        $this->formWidgets[] = array(
            'type' => 'container',
            'class' => WidgetContainer::class,
            'label' => 'container'
        );
        
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
            'editor' => SelectOptionsForm::class, 
            'label' => 'Select'
        );
        $this->formWidgets[] = array(
            'class' => ColorPickerField::class,
            'label' => 'Color picker'
        );
        
        
        $this->formWidgets = apply_filter('form-generator-form-widgets', $this->formWidgets);
        
        for($x=0; $x < count($this->formWidgets); $x++) {
            if (isset($this->formWidgets[$x]['type']) == false)
                $this->formWidgets[$x]['type'] = 'widget';
        }
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
