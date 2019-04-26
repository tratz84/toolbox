<?php

use core\controller\BaseController;

class fileController extends BaseController {
    
    
    public function action_index() {
        $f = get_data_file($_REQUEST['f']);
        
        if ($f == false) {
            die('File not found');
        }
        
        header('Content-Disposition: attachment; filename="' . basename($f) . '"');
        header('Content-type: '.mime_content_type($f));
        
        readfile($f);
        
    }
    
    
}

