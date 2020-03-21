<?php


namespace core\generator;

use core\template\DefaultTemplate;

class DAOGenerator {
    
    protected $databaseResource;
    protected $moduleName;
    protected $tableName;
    protected $columns;
    
    public function __construct($databaseResource, $moduleName, $tableName, $columns = null) {
        $this->databaseResource = $databaseResource;
        $this->moduleName = $moduleName;
        $this->tableName = $tableName;
        
        // fetch columns
        if ($columns === null) {
            $columns = queryList($databaseResource, 'describe '.$tableName);
        }
        
        $this->columns = $columns;
    }
    
    
    public function generate() {
        
        
        $modelDir = module_path($this->moduleName).'/lib/model';
        if (file_exists($modelDir.'/base') == false)
            mkdir($modelDir.'/base', 0755, true);
        
        // generate base-DBObject classes
        $tpl = new DefaultTemplate( lookupModuleFile('templates/generator/dbbaseobject.php') );
        $tpl->setVar('databaseResource', $this->databaseResource);
        $tpl->setVar('moduleName', $this->moduleName);
        $tpl->setVar('tableName', $this->tableName);
        $tpl->setVar('columns', $this->columns);
        
        
        $file = $modelDir . '/base/' . dbCamelCase($this->tableName) . 'Base.php';
        file_put_contents($file, $tpl->getTemplate());
        
        

        // generate DBObject classes
        $file = $modelDir . '/' . dbCamelCase($this->tableName) . '.php';
        if (file_exists($file) == false) {
            $tpl = new DefaultTemplate( lookupModuleFile('templates/generator/dbobject.php') );
            $tpl->setVar('databaseResource', $this->databaseResource);
            $tpl->setVar('moduleName', $this->moduleName);
            $tpl->setVar('tableName', $this->tableName);
            $tpl->setVar('columns', $this->columns);
            
            
            file_put_contents($file, $tpl->getTemplate());
        }
        
        
        // generate DAO classes
        $file = $modelDir . '/' . dbCamelCase($this->tableName) . 'DAO.php';
        if (file_exists($file) == false) {
            $tpl = new DefaultTemplate( lookupModuleFile('templates/generator/daobase.php') );
            $tpl->setVar('databaseResource', $this->databaseResource);
            $tpl->setVar('moduleName', $this->moduleName);
            $tpl->setVar('tableName', $this->tableName);
            $tpl->setVar('columns', $this->columns);
            
            
            file_put_contents($file, $tpl->getTemplate());
        }
        
        
    }
    
}
