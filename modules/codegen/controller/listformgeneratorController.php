<?php


use core\controller\BaseController;
use codegen\form\ListFormGeneratorForm;

class listformgeneratorController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new ListFormGeneratorForm();
        
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
        }
        
        
        
        
        return $this->render();
    }
    
    
    public function action_list() {
        
        $modules = module_list();
        $this->lists = array();
        foreach($modules as $modulename => $path) {
            $files = list_files($path . '/config/codegen/');
            if ($files) foreach($files as $f) {
                if (strpos($f, 'listform-') === 0 && strpos($f, '.php') !== false) {
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
    
    
}
