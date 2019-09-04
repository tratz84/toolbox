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
        $this->controller = $this;
        
        
        return $this->render();
    }
    
    
    public function action_delete() {
        
    }
    
    public function extensionSupported($file) {
        $p = strrpos($file, '.');
        
        if ($p === false) return false;
        
        $extension = strtolower( substr($file, $p+1) );
        
        return in_array($extension, ['css', 'php', 'js', 'json', 'html', 'htm', 'scss', 'sass', 'yml']) ? true : false;
    }
    
    public function editorMode($file) {
        $ext = file_extension($file);
        
        if ($ext == 'css') {
            return 'css';
        } else if ($ext == 'js') {
            return 'javascript';
        } else if ($ext == 'xml') {
            return 'xml';
        } else if ($ext == 'yml' || $ext == 'yaml') {
            return 'yaml';
        }
    
        
        return 'htmlmixed';
    }
    
    public function action_edit() {
        $this->templateName = $templateName = basename( get_var('n') );
        $this->file = $file = get_var('f');
        $this->controller = $this;
        
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
        
        if ($this->extensionSupported($f) == false) {
            $this->error = t('File extension not supported for editing');
            return $this->render();
        }
        
        if (is_post()) {
            $content = get_var('tacontent');
            if (file_put_contents($f, $content)) {
                report_user_message('File saved');
            } else {
                report_user_error('Error saving file');
            }
            
            redirect('/?m=fastsite&c=templateEditor&a=edit&n='.urlencode($this->templateName).'&f='.urlencode($this->file));
        }
        
//         $this->setShowDecorator(false);
        $this->content = file_get_contents( $f );
        
        return $this->render();
    }
    
    
    
    
    
}
