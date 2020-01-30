<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;

class csvController extends BaseController {
    
    
    public function action_index() {
        
        $f = basename(get_var('f'));
        
        $fullpath = get_data_file_safe('/tmp/', $f);
        if ($fullpath == false) {
            throw new InvalidStateException('CSV file not found');
        }
        
        
        return $this->render();
    }
    
    
    
}

