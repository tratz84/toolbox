<?php

namespace core\db\mysql;

use core\db\TableModel;
use core\db\DatabaseHandler;
use core\exception\DatabaseException;

class MysqlTableGenerator {
    
    protected $tableModel = null;
    
    protected $dbColumns = array();
    protected $dbConstraints = array();
    protected $dbIndexes = array();
    
    public function __construct(TableModel $model) {
        $this->tableModel = $model;
        
    }
    
    public function getTableName() {
        if ($this->tableModel->useSchemaInTableName()) {
            return $this->tableModel->getSchemaName().'__'.$this->tableModel->getTableName();
        } else {
            return $this->tableModel->getTableName();
        }
    }
    
    public function tableExists() {
        $mysql = DatabaseHandler::getInstance()::getConnection('default');
        
        try {
            $r = $mysql->query('describe `'.$this->getTableName().'`');
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    public function createSqlDiff() {
        if ($this->tableExists()) {
            return $this->buildAlter();
        } else {
            return $this->buildCreateTable();
        }
    }
    
    public function executeDiff() {
        
        $stats = $this->createSqlDiff();
        
        if (count($stats) == 0) {
            return 0;
        }
        
        $mysql = DatabaseHandler::getInstance()::getConnection('default');
        
        foreach($stats as $sql) {
            $r = $mysql->query( $sql );
            if ($r == false) {
                throw new DatabaseException('Error updating database: ' . $mysql->error);
            }
        }
        
        return count($stats);
    }
    
    
    protected function loadTableProperties() {
        $props = array();
        
        $mysql = DatabaseHandler::getInstance()::getConnection('default');
        
        $r = $mysql->query('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=?', array($mysql->getDatabaseName(), $this->getTableName()));
        while ($row = $r->fetch_assoc()) {
            $this->dbColumns[ $row['COLUMN_NAME'] ] = $row;
        }
        
        $sql = "select *
                from information_schema.key_column_usage kcu 
                join information_schema.table_constraints tc on (kcu.constraint_catalog = tc.constraint_catalog and kcu.table_schema = tc.table_schema and kcu.table_name=tc.table_name and kcu.CONSTRAINT_NAME=tc.CONSTRAINT_NAME)  
                where kcu.table_schema=? and kcu.table_name=? ";
        $r = $mysql->query($sql, array($mysql->getDatabaseName(), $this->getTableName()));
        while ($row = $r->fetch_assoc()) {
            $in = $row['CONSTRAINT_NAME'];
            if (isset($this->dbConstraints[$in]) == false) {
                $this->dbConstraints[$in] = array();
            }
            
            $this->dbConstraints[$in][] = $row;
        }
        
        $tbl = $mysql->getDatabaseName().'.'.$this->getTableName();
        $sql = "show keys from ".$tbl." where Non_unique=1";
        $r = $mysql->query($sql);
        while ($row = $r->fetch_assoc()) {
            $in = $row['Key_name'];
            if (isset($this->dbIndexes[$in]) == false) {
                $this->dbIndexes[$in] = array();
            }
            
            $this->dbIndexes[$in][] = $row;
        }
    }
    
    public function buildAlter() {
        $this->loadTableProperties();
        
        
        $sql1 = $this->buildAlterColumns();
//         $sql2 = $this->buildAlterUniqueConstraints();
        $sql3 = $this->buildAlterIndexes();
        
        return array_merge($sql1, $sql3);
    }
    
    protected function buildAlterColumns() {
        $sql_statements = array();
        
        // add/change columns
        $columns = $this->tableModel->getColumns();
        for($x=0; $x < count($columns); $x++) {
            $columnName = $columns[$x];
            
            $model_type = $this->tableModel->getColumnProperty($columnName, 'type');
            $model_default_val = $this->tableModel->getColumnProperty($columnName, 'default');
            
            if (isset($this->dbColumns[ $columnName ])) {
                if ($this->columnTypeChanged($columnName) == false) {
                    continue;
                }
                
                $sql = "";
                $sql = "ALTER TABLE  `" . $this->getTableName() . "` CHANGE COLUMN ";
                $sql .= $columnName . ' ' . $columnName . ' ' . $model_type;
                if ($model_default_val) {
                    $sql .= ' default \''.$model_default_val.'\'';
                }
                $sql .= ";";
                
                $sql_statements[] = $sql;
                
            } else {
                $sql = "";
                $sql .= "ALTER TABLE `" . $this->getTableName() . "` ADD COLUMN ";
                $sql .= '`'.$columnName . '` ' . $model_type;
                if ($model_default_val) {
                    $sql .= ' default \''.$model_default_val.'\'';
                }
                $sql .= ";";
                
                $sql_statements[] = $sql;
            }
        }
        
        // drop columns
        foreach($this->dbColumns as $columnName => $props) {
            if ($this->tableModel->hasColumn($columnName) == false) {
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` DROP COLUMN " . $columnName . ";";
            }
        }
        
        return $sql_statements;
    }
    
    
    protected function buildAlterUniqueConstraints() {
        $sql_statements = array();
        
        // ADD UNIQUE constraints
        $ucs = $this->tableModel->getUniqueConstraints();
        foreach($ucs as $key_name => $columns) {
            // already exists? => skip
            if (isset($this->dbConstraints[$key_name]))
                continue;
            
            $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` ADD UNIQUE KEY `" . $key_name . "`(`".implode('`, `', $columns)."`);";
        }
        
        // changed UNIQUE constraints
        foreach($this->dbConstraints as $key => $constraints) {
            // only handle UNIQUE
            if ($constraints[0]['CONSTRAINT_TYPE'] != 'UNIQUE')
                continue;
            
            // constraint removed?
            if ($this->tableModel->hasUniqueConstraint($key) == false) {
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` DROP KEY `" . $key . "`;";
                continue;
            }
            
            // constraint changed?
            $model_constraints = $this->tableModel->getUniqueConstraint($key);
            
            $changed = false;
            
            if (count($constraints) != count($model_constraints)) {
                $changed = true;
            } else {
                foreach($constraints as $db_constraint) {
                    if (in_array($db_constraint['COLUMN_NAME'], $model_constraints) == false) {
                        $changed = true;
                        break;
                    }
                }
            }
            
            if ($changed) {
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` DROP KEY `" . $key . "`;";
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` ADD UNIQUE KEY `" . $key . "`(`".implode('`, `', $model_constraints)."`);";
                
            }
        }
        
        return $sql_statements;
    }
    
    
    
    protected function buildAlterIndexes() {
        $sql_statements = array();
        
        // ADD indexes
        $ix = $this->tableModel->getIndexes();
        foreach($ix as $indexName => $props) {
            // already exists? => skip
            if (isset($this->dbIndexes[$indexName]))
                continue;
            
            if (isset($props['unique']) && $props['unique']) {
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` ADD UNIQUE `" . $indexName . "`(`".implode('`, `', $props['columns'])."`);";
            } else {
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` ADD INDEX `" . $indexName . "`(`".implode('`, `', $props['columns'])."`);";
            }
        }
        
        // changed constraints
        foreach($this->dbIndexes as $key => $indexes) {
            // constraint removed?
            if ($this->tableModel->hasIndex($key) == false) {
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` DROP KEY `" . $key . "`;";
                continue;
            }
            
            // constraint changed?
            $model_index = $this->tableModel->getIndex($key);
            
            $changed = false;
            
            // column-count changed?
            if (count($indexes) != count($model_index['columns'])) {
                $changed = true;
            }
            else {
                foreach($indexes as $db_index) {
                    if (in_array($db_index['Column_name'], $model_index['columns']) == false) {
                        $changed = true;
                        break;
                    }
                    else if ($db_index['Non_unique'] == '1' && isset($model_index['unique']) && $model_index['unique']) {
                        $changed = true;
                        break;
                    }
                }
            }
            
            
            if ($changed) {
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` DROP KEY `" . $key . "`;";
                if (isset($props['unique']) && $props['unique']) {
                    $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` ADD UNIQUE `" . $indexName . "`(`".implode('`, `', $model_index['columns'])."`);";
                } else {
                    $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` ADD INDEX `" . $key . "`(`".implode('`, `', $model_index['columns'])."`);";
                }
                
            }
        }
        
        return $sql_statements;
    }
    
    
    
    
    protected function columnTypeChanged($columnName) {
        $model_type = $this->tableModel->getColumnProperty($columnName, 'type');
        $db_type = $this->dbColumns[$columnName]['DATA_TYPE'];
        
        $model_type = $this->normalizeType($model_type);
        $db_type = $this->normalizeType($db_type);
        
        if ($db_type == $model_type) {
            return false;
        }
        
        if ($model_type == $this->dbColumns[$columnName]['COLUMN_TYPE']) {
            return false;
        }
        
        return true;
    }
    
    protected function normalizeType($t) {
        if ($t == 'tinyint') {
            $t = 'bool';
        }
        if ($t == 'bool') {
            $t = 'boolean';
        }
        
        return $t;
    }
    
    
    public function buildCreateTable() {
        $m = $this->tableModel;
        
        $sql = 'CREATE TABLE '.$this->getTableName().' ('.PHP_EOL;
        $columns = $m->getColumns();
        for($colno=0; $colno < count($columns); $colno++) {
            $c = $columns[$colno];
            $sql .= "\t`$c`";

            $sql .= ' ' . $m->getColumnProperty( $c, 'type');
            if ($key = $m->getColumnProperty($c, 'key')) {
                $sql .= ' ' . $key;
            }
            if ($m->getColumnProperty($c, 'auto_increment')) {
                $sql .= ' AUTO_INCREMENT';
            }
            
            if ($colno < count($columns)-1)
                $sql .= ",\n";
            
        }
        
        $indexes = $this->tableModel->getIndexes();
        $constraint_keys = array_keys($indexes);
        $counter = 0;
        for($x=0; $x < count($constraint_keys); $x++) {
            $key = $constraint_keys[$x];
            
            if ($x == 0) {
                $sql .= ",\n";
            }
            
            $cols = $indexes[$key]['columns'];

            if (isset($indexes[$key]['unique']) && $indexes[$key]['unique']) {
                // UNIQUE constraint
                $sql .= "\tCONSTRAINT `{$key}` UNIQUE(".implode(', ', $cols) . ")";
            } else {
                // standard INDEX
                $sql .= "\tKEY `{$key}` (".implode(', ', $cols) . ")";
            }
            
            $sql .= ($x < count($constraint_keys)-1 ? ",\n":"");
            
            $counter++;
        }
        
        
        $sql .= PHP_EOL.') ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci'.PHP_EOL;
        
        return array( $sql );
    }
    
    
    /**
     * updateModule() - loads '<module>/config/tablemodel.php' & applies TableModel-changes
     */
    public static function updateModule($moduleName, $outputSql=false) {
        // get file with models
        $file_tablemodel = module_file($moduleName, 'config/tablemodel.php');
        if (!$file_tablemodel) {
            return false;
        }
        
        $tablemodels = load_php_file( $file_tablemodel );
        
        if (is_array($tablemodels)) {
            foreach($tablemodels as $tm) {
                $mtg = new MysqlTableGenerator( $tm );
                
                if ($outputSql) {
                    // DEBUG database changes?
                    var_export( $mtg->createSqlDiff() );
                } else {
                    $mtg->executeDiff();
                }
            }
        }
        
        return true;
    }
    
    
}


