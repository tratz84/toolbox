<?php


use core\controller\BaseController;
use core\exception\FileException;


class infoController extends BaseController {
    
    
    
    public function action_index() {
        
        $f = get_var('f');
        $fullpath = get_data_file_safe('fastsite/fs-media/', $f);
        $basepath = get_data_file_safe('fastsite/fs-media/', '/');
        
        if (!$fullpath) {
            throw new FileException('File not found');
        }
        
        $this->f = $f;
        $this->filename = basename($f);
        $this->path = substr($fullpath, strlen($basepath));
        $this->filesize = filesize($fullpath);
        
        
        if (gd_image_supported($this->filename)) {
            $img = gd_load_image($fullpath);
            $this->img_width = imagesx($img);
            $this->img_height= imagesy($img);
        }
        
        
        return $this->render();
    }
    
}

