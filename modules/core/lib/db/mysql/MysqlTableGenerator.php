<?php

namespace core\db\mysql;

use core\db\TableModel;
use core\db\DatabaseHandler;
use core\exception\DatabaseException;
use core\exception\InvalidStateException;

class MysqlTableGenerator {
    
    protected $resourceName = null;
    protected $tableModel = null;
    
    protected $dbColumns = array();
    protected $dbConstraints = array();
    protected $dbIndexes = array();
    protected $dbForeignKeys = array();
    
    public function __construct(TableModel $model) {
        $this->tableModel = $model;
        
        $this->resourceName = $model->getResourceName();
        
    }
    
    public function getTableName() {
        if ($this->tableModel->useSchemaInTableName()) {
            return $this->tableModel->getSchemaName().'__'.$this->tableModel->getTableName();
        } else {
            return $this->tableModel->getTableName();
        }
    }
    
    public function tableExists() {
        $mysql = DatabaseHandler::getInstance()::getConnection( $this->resourceName );
        
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
        
        $mysql = DatabaseHandler::getInstance()::getConnection( $this->resourceName );
        
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
        
        $mysql = DatabaseHandler::getInstance()::getConnection( $this->resourceName );
        
        // column info
        $r = $mysql->query('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=?', array($mysql->getDatabaseName(), $this->getTableName()));
        while ($row = $r->fetch_assoc()) {
            $this->dbColumns[ $row['COLUMN_NAME'] ] = $row;
        }
        
        // constraints info
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
        
        // indexes
        $tbl = $mysql->getDatabaseName().'.'.$this->getTableName();
        $sql = "show keys from ".$tbl;//." where Non_unique=1";
        $r = $mysql->query($sql);
        while ($row = $r->fetch_assoc()) {
            $in = $row['Key_name'];
            if (isset($this->dbIndexes[$in]) == false) {
                $this->dbIndexes[$in] = array();
            }
            
            $this->dbIndexes[$in][] = $row;
        }
        
        
        // foreign keys
        $sql = "SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE REFERENCED_TABLE_SCHEMA = ?
                    AND TABLE_NAME = ? 
                ORDER BY ORDINAL_POSITION";
        $r = $mysql->query($sql, array($mysql->getDatabaseName(), $this->getTableName()));
        while ($row = $r->fetch_assoc()) {
            $fk_name = $row['CONSTRAINT_NAME'];
            
            if (isset($this->dbForeignKeys[$fk_name]) == false) {
                $this->dbForeignKeys[$fk_name] = array();
                $this->dbForeignKeys[$fk_name]['columns'] = array();
                $this->dbForeignKeys[$fk_name]['ref_table'] = $row['REFERENCED_TABLE_NAME'];
                $this->dbForeignKeys[$fk_name]['ref_columns'] = array();
                
                $sql = "select *
                        FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
                        WHERE CONSTRAINT_SCHEMA = ? 
                                AND TABLE_NAME = ?
                                AND CONSTRAINT_NAME = ?";
                $ref_constraint = $mysql->queryOne($sql, array($mysql->getDatabaseName(), $this->getTableName(), $row['CONSTRAINT_NAME']));
                
                if (!$ref_constraint) {
                    // this shouldn't never happen afaik. Remove in future when sure?
                    throw new DatabaseException('Unable to lookup referential_constraints');
                }
                
                $this->dbForeignKeys[$fk_name]['on_update'] = $ref_constraint['UPDATE_RULE'];
                $this->dbForeignKeys[$fk_name]['on_delete'] = $ref_constraint['DELETE_RULE'];
            }
            
            $this->dbForeignKeys[$fk_name]['columns'][] = $row['COLUMN_NAME'];
            $this->dbForeignKeys[$fk_name]['ref_columns'][] = $row['REFERENCED_COLUMN_NAME'];
        }
        
    }
    
    public function buildAlter() {
        $this->loadTableProperties();
        
        // check if tableModel is valid
        $this->tableModel->checkSanity();
        
        $sql1 = $this->buildRenameColumns();
        $sql2 = $this->buildAlterColumns();
        $sql3 = $this->buildAlterIndexes();
        $sql4 = $this->buildAlterForeignKeys();
        
        return array_merge($sql1, $sql2, $sql3, $sql4);
    }
    
    
    
    protected function buildRenameColumns() {
        $sql_statements = array();
        
        $renamedColumns = $this->tableModel->getRenamedColumns();
        
        foreach($renamedColumns as $old => $new) {
            if (isset($this->dbColumns[ $new ]) == false) {
                // create statement
                $sql_statements[] = 'ALTER TABLE `'.$this->getTableName().'` RENAME COLUMN `'.$old.'` TO `'.$new.'`';
                
                // update db-model
                $this->dbColumns[ $new ] = $this->dbColumns[ $old ];
                unset( $this->dbColumns[ $old ] );
            }
        }
        
        return $sql_statements;
    }
    
    protected function buildAlterColumns() {
        $sql_statements = array();
        
        // add/change columns
        $columns = $this->tableModel->getColumns();
        for($x=0; $x < count($columns); $x++) {
            $columnName = $columns[$x];
            
            $model_type = $this->tableModel->getColumnProperty($columnName, 'type');
            $model_default_val = $this->tableModel->getColumnProperty($columnName,  $this->resourceName );
            
            if (isset($this->dbColumns[ $columnName ])) {
                if ($this->columnTypeChanged($columnName) == false) {
                    continue;
                }
                
                $sql = "";
                $sql = "ALTER TABLE  `" . $this->getTableName() . "` CHANGE COLUMN ";
                $sql .= '`' . $columnName . '` `' . $columnName . '` ' . $model_type;
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
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` DROP COLUMN `" . $columnName . "`;";
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
                $indexProps = '';
                if (isset($props['fulltext']) && $props['fulltext']) {
                    $indexProps = ' FULLTEXT ';
                }
                
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` ADD {$indexProps} INDEX `" . $indexName . "`(`".implode('`, `', $props['columns'])."`);";
            }
        }
        
        // changed constraints
        foreach($this->dbIndexes as $key => $indexes) {
            // skip primary-key indexes
            if ($key == 'PRIMARY') {
                continue;
            }
            
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
                    } else if ($db_index['Index_type'] == 'FULLTEXT' && (isset($model_index['fulltext']) == false || $model_index['fulltext'] == false)) {
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
                    $indexProps = '';
                    if (isset($props['fulltext']) && $props['fulltext']) {
                        $indexProps = ' FULLTEXT ';
                    }

                    $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` ADD {$indexProps} INDEX `" . $key . "`(`".implode('`, `', $model_index['columns'])."`);";
                }
                
            }
        }
        
        
        // handle PRIMARY KEY's
        foreach($this->dbIndexes as $key => $indexes) {
            if ($key != 'PRIMARY')
                continue;
            
            if ($this->getTableName() != 'base__multiuser_lock')
                continue;
            
            // fetch PK's from model
            $model_pks = $this->tableModel->getPrimaryKeys();
            
            $changed = false;
            if (count($model_pks) != count($indexes)) {
                $changed = true;
            } else {
                foreach($indexes as $i) {
                    if (in_array($i['Column_name'], $model_pks) == false) {
                        $changed = true;
                        break;
                    }
                }
            }
            
            if ($changed) {
                $sql_statements[] = "ALTER TABLE base__multiuser_lock DROP PRIMARY KEY;";
                $sql_statements[] = "ALTER TABLE base__multiuser_lock ADD PRIMARY KEY (`". implode('`, `', $model_pks) . "`);";
            }
        }
        
        
        return $sql_statements;
    }
    
    
    
    protected function buildAlterForeignKeys() {
        $sql_statements = array();
        
        // add/change foreign keys
        $foreignKeys = $this->tableModel->getForeignKeys();
        $fkNames = array_keys($foreignKeys);
        
        for($x=0; $x < count($fkNames); $x++) {
            $fkName = $fkNames[$x];
            $foreignKey = $foreignKeys[$fkName];
            
            if (isset($this->dbForeignKeys[$fkName])) {
                // TODO: check if FK is changed
                $changed = false;
                
                if (count($this->dbForeignKeys[$fkName]['columns']) != count($foreignKey['columns'])) {
                    $changed = true;
                } else {
                    for($y=0; $y < count($this->dbForeignKeys[$fkName]['columns']); $y++) {
                        if ($this->dbForeignKeys[$fkName]['columns'][$y] != $foreignKey['columns'][$y]) {
                            $changed = true;
                            break;
                        }
                    }
                }
                
                if (count($this->dbForeignKeys[$fkName]['ref_columns']) != count($foreignKey['ref_columns'])) {
                    $changed = true;
                } else {
                    for($y=0; $y < count($this->dbForeignKeys[$fkName]['ref_columns']); $y++) {
                        if ($this->dbForeignKeys[$fkName]['ref_columns'][$y] != $foreignKey['ref_columns'][$y]) {
                            $changed = true;
                            break;
                        }
                    }
                }
                
                if ($this->dbForeignKeys[$fkName]['ref_table'] != $foreignKey['ref_table']) {
                    $changed = true;
                }
                if ($this->dbForeignKeys[$fkName]['on_delete'] != $foreignKey['on_delete']) {
                    $changed = true;
                }
                if ($this->dbForeignKeys[$fkName]['on_update'] != $foreignKey['on_update']) {
                    $changed = true;
                }
                
                if ($changed) {
                    $sql_statements[] = 'ALTER TABLE `'.$this->getTableName().'` DROP CONSTRAINT `'.$fkName.'`';
                } else {
                    // not changed? => skip
                    continue;
                }
            }
            
            // build add constraint-sql
            $sql = '';
            $sql .= 'ALTER TABLE `'.$this->getTableName().'` ADD CONSTRAINT `'.$fkName.'`';
            $sql .= ' FOREIGN KEY (`'.implode('`, `', $foreignKey['columns']).'`)';
            $sql .= ' REFERENCES `'.$foreignKey['ref_table'].'` (`'.implode('`, `', $foreignKey['ref_columns']).'`)';
            $sql .= ' ON DELETE '.$foreignKey['on_delete'].' ON UPDATE '.$foreignKey['on_update'].';';
            $sql_statements[] = $sql;
        }
        
        // drop foreign keys
//         var_export($foreignKeys);exit;
        foreach($this->dbForeignKeys as $fkName => $props) {
            if (isset($foreignKeys[$fkName]) == false) {
                $sql_statements[] = "ALTER TABLE `" . $this->getTableName() . "` DROP CONSTRAINT `" . $fkName . "`;";
            }
        }
        
        
        return $sql_statements ;
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
        
        $primaryKeyColumns = array();
        
        $sql = 'CREATE TABLE '.$this->getTableName().' ('.PHP_EOL;
        $columns = $m->getColumns();
        for($colno=0; $colno < count($columns); $colno++) {
            if ($colno > 0) {
                $sql .= ",\n";
            }
            
            $c = $columns[$colno];
            $sql .= "\t`$c`";

            $sql .= ' ' . $m->getColumnProperty( $c, 'type');
            if ($key = $m->getColumnProperty($c, 'key')) {
                if ($key == 'PRIMARY KEY') {
                    $primaryKeyColumns[] = $c;
                }
                else {
                    $sql .= ' ' . $key;
                }
            }
            if ($m->getColumnProperty($c, 'auto_increment')) {
                $sql .= ' AUTO_INCREMENT';
            }
            
        }
        
        $indexes = $this->tableModel->getIndexes();
        $constraint_keys = array_keys($indexes);
        $counter = 0;
        for($x=0; $x < count($constraint_keys); $x++) {
            $key = $constraint_keys[$x];
            
            $sql .= ",\n";
            
            $cols = $indexes[$key]['columns'];

            if (isset($indexes[$key]['unique']) && $indexes[$key]['unique']) {
                // UNIQUE constraint
                $sql .= "\tCONSTRAINT `{$key}` UNIQUE(".implode(', ', $cols) . ")";
            } else {
                // standard INDEX
                $indexProps = '';
                if (isset($indexes[$key]['fulltext']) && $indexes[$key]['fulltext']) {
                    $indexProps = 'FULLTEXT ';
                }

                $sql .= "\t{$indexProps}KEY `{$key}` (".implode(', ', $cols) . ")";
            }
            
//             $sql .= ($x < count($constraint_keys)-1 ? ",\n":"");
            
            $counter++;
        }
        
        // build FK's
        $foreignKeys = $this->tableModel->getForeignKeys();
        foreach($foreignKeys as $keyName => $fk) {
            $sql .= ",\n";
            
            $fk_sql = '';
            $fk_sql .= "\tCONSTRAINT `".$keyName."` FOREIGN KEY";
            $fk_sql .= " (`".implode("`, `", $fk['columns'])."`)";
            $fk_sql .= " REFERENCES `".$fk['ref_table']."`";
            $fk_sql .= " (`".implode("`, `", $fk['ref_columns'])."`)";
            $fk_sql .= " ON DELETE " . strtoupper($fk['on_delete']);
            $fk_sql .= " ON UPDATE " . strtoupper($fk['on_update']);
            
            $sql .= $fk_sql;
        }
        
        
        if (count($primaryKeyColumns)) {
            $sql .= ",\n" . "\tPRIMARY KEY (" . implode(', ', $primaryKeyColumns) . ")";
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
        
        $changedTableModels = array();
        
        $tablemodels = load_php_file( $file_tablemodel );
        
        if (is_array($tablemodels)) {
            foreach($tablemodels as $tm) {
                $mtg = new MysqlTableGenerator( $tm );
                
                if ($outputSql) {
                    // DEBUG database changes?
                    $diff = $mtg->createSqlDiff();
                    
                    var_export( $diff );
                    
                    if (count($diff)) {
                        $changedTableModels[] = $tm;
                    }
                } else {
                    $countQueries = $mtg->executeDiff();
                    
                    if ($countQueries) {
                        $changedTableModels[] = $tm;
                    }
                }
            }
        }
        
        return $changedTableModels;
    }
    
    
}


