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
        
        $tm = new TableModel('project', 'project');
        $tm->addColumn('project_id', 'int');
        $tm->setPrimaryKey('project_id');
        $tm->addColumn('person_id', 'int');
        $tm->addColumn('company_id', 'int');
        $tm->addColumn('company_id', 'bigint');
        $tm->addColumn('project_name', 'varchar(255)');
        $tm->addColumn('active', 'bool');
        // $tm->addColumn('note', 'text');
        $tm->addColumn('edited', 'datetime');
        $tm->addColumn('created', 'datetime');
        
        $mtg = new MysqlTableGenerator( $tm );
        print $mtg->createTableUpdate();
        
        exit;
        
        return $this->render();
    }
    
    
    public function action_import_existing_table() {
        
    }
    
    
}

