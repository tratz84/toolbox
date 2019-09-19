<?php


use core\controller\BaseController;
use core\exception\FileException;
use core\Context;

class filesController extends BaseController {
    
    
    public function action_index() {
        
        $this->files = $this->listFiles();
        
        
        return $this->render();
    }
    
    
    protected function listFiles($folder='/') {
        // check if mediafolder exists
        $mediafolder = get_data_file('fastsite/fs-media');
        if ($mediafolder == false) {
            $p = Context::getInstance()->getDataDir() . '/fastsite/fs-media';
            if (mkdir($p, 0755, true) == false) {
                throw new FileException('Unable to create fs-media folder');
            }
        }
        
        
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
