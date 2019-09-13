<?php


use core\controller\BaseController;
use core\exception\FileException;

class editImageController extends BaseController {
    
    
    public function action_index() {
        
        $f = get_var('f');
        $fullpath = get_data_file_safe('fastsite/fs-media/', $f);
        $basepath = get_data_file_safe('fastsite/fs-media/', '/');
        
        if (!$fullpath) {
            throw new FileException('File not found');
        }
        
        if (strpos($fullpath, $basepath) !== 0) {
            throw new FileException('Invalid path');
        }
        
        $this->f = $f;
        $this->filename = basename($f);
        $this->path = substr($fullpath, strlen($basepath));
        $this->filesize = filesize($fullpath);
        $this->imgUrl = BASE_HREF . 'fs-media'. $this->path;
        
        return $this->render();
    }
    
    
    public function action_resize() {
        
        
        return $this->render();
    }
    
    public function action_croprotate() {
        
        
        return $this->render();
    }
    
    
}
