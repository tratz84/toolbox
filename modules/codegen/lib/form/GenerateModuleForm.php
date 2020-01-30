<?php

namespace codegen\form;


use core\Context;
use core\forms\BaseForm;
use core\forms\SelectField;
use core\forms\TextField;

class GenerateModuleForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        
        $moduleDirs = Context::getInstance()->getModuleDirs();
        $mapModuleDirs = array();
        foreach($moduleDirs as $md) {
            $mapModuleDirs[$md] = realpath( $md );
        }
        $this->addWidget(new SelectField('module_dir', '', $mapModuleDirs, 'Module dir'));
        
        
        $this->addWidget(new TextField('module_code', '', 'Module code'));
        $this->addWidget(new TextField('module_name', '', 'Module name'));
        $this->addWidget(new TextField('module_desc', '', 'Module description'));
        

        $this->addValidator('module_dir', function($form) use ($moduleDirs) {
            $md = $form->getWidgetValue('module_dir');
            
            if (in_array($md, $moduleDirs) == false) {
                return 'Invalid module dir';
            }
        });
        
        $this->addValidator('module_code', function($form) use ($moduleDirs) {
            $mn = trim( $form->getWidgetValue('module_code') );
            if ($mn == '') {
                return 'required';
            }
            if (strlen($mn) < 3) {
                return 'minimum length 3 chars';
            }
            
            if (preg_match('/^[a-zA-Z0-9_]+$/', $mn) == false) {
                return 'only allowed characters: a-z, A-Z, 0-9, _';
            }
            
            foreach($moduleDirs as $md) {
                $p = $md . '/' . $mn;
                
                if (file_exists($p)) {
                    return 'duplicate module name';
                }
            }
        });
        
        $this->addValidator('module_name', function($form) use ($moduleDirs) {
            $mn = trim( $form->getWidgetValue('module_code') );
            if ($mn == '') {
                return 'required';
            }
        });
        
    }
    
}
