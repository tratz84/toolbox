<?php


use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\db\mysql\MysqlTableGenerator;
use core\db\TableModel;

class editorController extends BaseController {
    
    
    public function action_index() {
        
        $this->mod = get_var('mod');
        if (module_exists($this->mod) == false)
            throw new InvalidStateException('Module not found');
        
        
        return $this->render();
    }
    
    
    public function action_import_existing_table() {
        
    }
    
    
}

