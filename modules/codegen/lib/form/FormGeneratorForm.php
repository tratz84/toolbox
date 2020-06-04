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
        
        $this->addWidget(new HiddenField('treedata'));
        $this->addModuleSelection();
        $this->addWidget(new TextField('form_name', '', 'Form name'));
        $this->addWidget(new SelectField('daoClass', '', codegen_map_dao_classes(), 'DAO Object'));
        $this->addWidget(new TextField('key_fields', '', 'Key fields'));
        $this->getWidget('key_fields')->setInfoText('Fields used for locking. Comma separated');
        $this->addWidget(new TextField('short_description', '', 'Description'));
        
        
        
        $this->addValidator('module_name', new NotEmptyValidator());
        $this->addValidator('form_name', function($form) {
            $n = $form->getWidgetValue('form_name');
            
            if (preg_match('/^[a-zA-Z0-9_\\\\]+$/', $n) == false) {
                return 'invalid name';
            }
            if (endsWith($n, 'Form') == false) {
                return 'name must end with "Form"';
            }
            
        });
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
        
        
        $map = codegen_map_modules();
        
        $this->addWidget(new SelectField('module_name', '', $map, 'Module'));
        
    }
    
    
}
