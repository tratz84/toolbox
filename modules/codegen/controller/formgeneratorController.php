<?php


use core\controller\BaseController;
use codegen\form\FormGeneratorForm;
use core\exception\InvalidStateException;
use core\exception\FileException;

class formgeneratorController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new FormGeneratorForm();
        
        if (is_get()) {
            
            $form_module = get_var('fm');
            $form_file = get_var('ff');
            $codegen_path = module_file($form_module, '/config/codegen');
            $fullpath = realpath($codegen_path. '/' . $form_file);
            
            if ($fullpath === false) {
                throw new FileException('File not found');
            }
            if (strpos($fullpath, $codegen_path) !== 0) {
                throw new FileException('Invalid location');
            }
            
            $formdata = include $fullpath;
            $this->form->bind($formdata);
        }
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            $this->form->validate();
            
            // save
            $f = module_file(get_var('module_name'), '/');
            if ($f !== false) {
                if (file_exists($f . '/config/codegen') == false) {
                    if (mkdir($f . '/config/codegen', 0755, true) == false) {
                        throw new FileException('Unable to create save-dir');
                    }
                }
                
                $form = slugify($_REQUEST['form_name']);
                file_put_contents($f.'/config/codegen/form-'.$form.'.php', "<?php\n\nreturn ".var_export($_REQUEST, true) . ";\n\n");
            }
        }
        
        return $this->render();
    }
    
    
    public function action_list() {
        
        $modules = module_list();
        $this->forms = array();
        foreach($modules as $modulename => $path) {
            $files = list_files($path . '/config/codegen/');
            if ($files) foreach($files as $f) {
                if (strpos($f, 'form-') === 0 && strpos($f, '.php') !== false) {
                    $this->forms[] = array(
                        'module' => $modulename,
                        'path' => realpath($path . '/config/codegen/' . $f),
                        'file' => $f
                    );
                }
            }
        }
        
        return $this->render();
    }
    
    
    
    public function action_select_widget() {
        $form = new FormGeneratorForm();
        
        $this->formWidgets = $form->getFormWidgets();
        
        $this->setShowDecorator( false );
        
        return $this->render();
    }
    
    
    public function action_widget_properties() {

        
        $this->setShowDecorator( false );
        
        return $this->render();
    }
    
    
    
}

