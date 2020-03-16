<?php


use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\db\mysql\MysqlTableGenerator;
use core\db\TableModel;

class editorController extends BaseController {
    
    
    public function action_index() {
        $this->mod = get_var('mod');
        if (module_exists($this->mod) == false) {
            throw new InvalidStateException('Module not found');
        }
        
        
        $this->data_tablemodel = array();
        $file_tablemodel = module_file($this->mod, 'config/tablemodel.php');
        if ($file_tablemodel) {
            $this->data_tablemodel = load_php_file( module_file($this->mod, 'config/tablemodel.php') );
        }
        
        
        
        if (is_post()) {
            
        }
        
        
        return $this->render();
    }
    
    
    public function action_import_existing_table() {
        
    }
    
    
}

