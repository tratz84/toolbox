<?php


use core\controller\BaseController;
use core\exception\InvalidStateException;

class templateEditorController extends BaseController {
    
    
    public function action_index() {
        
        $p = get_data_file('fastsite/templates/'.basename(get_var('n')));
        
        if (!$p)
            throw new InvalidStateException('Template not found');
        
        
        $this->templateName = get_var('n');
        $this->files = list_files($p, ['recursive' => true]);
        
        
        
        return $this->render();
    }
    
    
    public function action_delete() {
        
    }
    
    public function action_edit() {
        
        $this->templateName = $templateName = basename( get_var('n') );
        $this->file = $file = get_var('f');
        
        $templateDir = get_data_file('fastsite/templates');
        
        $f = get_data_file('fastsite/templates/'.$templateName.'/'.$file);
        
        // check if file is in template dir
        if (strpos($f, $templateDir) === false) {
            $this->error = t('File not found');
            return $this->render();
        }
        
        if (is_dir($f)) {
            $this->error = t('Selected file is a directory');
            return $this->render();
        }
        
        $extension = strtolower( substr($f, strrpos($f, '.')+1) );
        if (in_array($extension, ['css', 'php', 'js', 'html', 'htm', 'scss', 'sass', 'yml']) == false) {
            $this->error = t('File extension not supported for editing');
            return $this->render();
        }
        
        
        return $this->render();
    }
    
    
    
    
    
}
