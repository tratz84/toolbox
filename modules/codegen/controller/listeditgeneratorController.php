<?php


use core\controller\BaseController;
use codegen\form\ListEditGeneratorForm;

class listeditgeneratorController extends BaseController {
    
    
    
    public function action_index() {
        
        $this->form = new ListEditGeneratorForm();
        
        
        if (is_get() && get_var('fm') && get_var('ff')) {
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
            
            if ($this->form->validate()) {
                // save
                $module_name = $this->form->getWidgetValue('module_name');
                $f = module_file($module_name, '/');
                if ($f === false) {
                    throw new InvalidStateException('Module not found');
                }
                if (file_exists($f . '/config/codegen') == false) {
                    if (mkdir($f . '/config/codegen', 0755, true) == false) {
                        throw new FileException('Unable to create save-dir');
                    }
                }
                
                $name = slugify($_REQUEST['name']);
                $formfile = 'listeditor-'.$name.'.php';
                
                $data = $this->form->asArray();
                file_put_contents($f.'/config/codegen/'.$formfile, "<?php\n\nreturn ".var_export($data, true) . ";\n\n");
                
                $generator = new codegen\generator\ListEditGenerator();
                if ($generator->loadData( $module_name, $formfile )) {
                    $generator->generate();
                }
                
                redirect( '/?m=codegen&c=listeditgenerator&fm='.urlencode($module_name).'&ff='.urlencode($formfile) );
            }
            
        }
        
        
        return $this->render();
    }
    
    
    public function action_list() {
        
        $modules = module_list();
        $this->lists = array();
        foreach($modules as $modulename => $path) {
            $files = list_files($path . '/config/codegen/');
            if ($files) foreach($files as $f) {
                if (strpos($f, 'listeditor-') === 0 && strpos($f, '.php') !== false) {
                    $data = include realpath($path . '/config/codegen/' . $f);
                    $this->lists[] = array(
                        'module' => $modulename,
                        'path' => realpath($path . '/config/codegen/' . $f),
                        'file' => $f,
                        'short_description' => isset($data['short_description']) ? $data['short_description'] : ''
                    );
                }
            }
        }
        
        return $this->render();
    }
    
    
    public function action_delete() {
        
        $generator = new codegen\generator\ListEditGenerator();
        $generator->delete( get_var('fm'), get_var('ff') );
        
        
        redirect('/?m=codegen&c=listeditgenerator&a=list');
    }
    
    
    
}
