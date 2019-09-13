<?php


use core\controller\BaseController;
use core\exception\FileException;


class infoController extends BaseController {
    
    
    
    public function action_index() {
        
        $f = get_var('f');
        $f = get_data_file_safe('fastsite/fs-media/', $f);
        $basepath = get_data_file_safe('fastsite/fs-media/', '/');
        
        if (!$f) {
            throw new FileException('File not found');
        }
        
        $this->filename = basename($f);
        $this->path = substr($f, strlen($basepath));
        $this->filesize = filesize($f);
        
        
        return $this->render();
    }
    
}

