<?php

use core\controller\BaseController;

class fileController extends BaseController {
    
    
    public function action_index() {
        $f = get_data_file($_REQUEST['f']);
        
        if ($f == false) {
            die('File not found');
        }
        
        header('Content-Disposition: attachment; filename="' . basename($f) . '"');
        header('Content-type: '.file_mime_type($f));
        
        readfile($f);
        
    }
    
    
}

