<?php


use core\controller\BaseController;
use core\exception\FileException;

class filesController extends BaseController {
    
    
    public function action_index() {
        
        $this->files = $this->listFiles();
        
        
        return $this->render();
    }
    
    
    protected function listFiles($folder='/') {
        $f = get_data_file_safe('fastsite/fs-media', $folder);
        
        if ($f === false) {
            throw new FileException('Invalid folder requested');
        }
        
        return list_files($f);
    }
    
    
    
    public function action_delete() {
        $f = get_var('f');
        $f = get_data_file_safe('fastsite/fs-media/', $f);
        
        if (!$f) {
            throw new FileException('File not found');
        }
        
        
        unlink( $f );
        
        redirect('/?m=fastsite&c=media/files');
    }
    
}
