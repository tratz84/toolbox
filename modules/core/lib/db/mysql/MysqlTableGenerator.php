<?php

namespace core\db\mysql;

use core\db\TableModel;
use core\db\DatabaseHandler;

class MysqlTableGenerator {
    
    protected $tableModel = null;
    
    public function __construct(TableModel $model) {
        $this->tableModel = $model;
        
    }
    
    public function getTableName() {
        return $this->tableModel->getSchemaName().'__'.$this->tableModel->getTableName();
    }
    
    public function tableExists() {
        $mysql = DatabaseHandler::getInstance()::getConnection('default');
        
        try {
            $r = $mysql->query('describe `'.$this->getTableName().'`');
            return true;
        } catch (\core\exception\QueryException $ex) {
            return false;
        }
    }
    
    public function createTableUpdate() {
        if ($this->tableExists()) {
            return $this->buildAlter();
        } else {
            return $this->buildCreateTable();
        }
    }
    
    protected function tableProperties() {
        $props = array();
        
        $mysql = DatabaseHandler::getInstance()::getConnection('default');
        $r = $mysql->query('describe `'.$this->getTableName().'`');
        
        while ($row = $r->fetch_assoc()) {
            $props[ $row['Field'] ] = $row;
        }
        
        return $props;
    }
    
    public function buildAlter() {
        $props = $this->tableProperties();
        
        $sql = '';
        
        // TODO: add/change columns
        $columns = $this->tableModel->getColumns();
        for($x=0; $x < count($columns); $x++) {
            $columnName = $columns[$x];
            
            $model_type = $this->tableModel->getColumnProperty($columnName, 'type');
            $model_default_val = $this->tableModel->getColumnProperty($columnName, 'default');
            
            if (isset($props[ $columnName ])) {
                $db_type = $props[ $columnName ];
                
                if ($this->typesEqual($db_type, $model_type)) {
                    continue;
                }
            } else {
                $sql .= "ALTER TABLE `" . $this->getTableName() . "` ADD COLUMN ";
                $sql .= '`'.$columnName . '` ' . $model_type;
                if ($model_default_val) {
                    $sql .= ' default \''.$model_default_val.'\'';
                }
                
                // TODO: after.. ?
            }
        }
        
        // TODO: drop columns
        
        $sql = 'blabla';
        
        return $sql;
    }
    
    protected function typesEqual($type1, $type2) {
        $type1 = $this->normalizeType($type1);
        $type2 = $this->normalizeType($type2);
        
        if ($type1 == $type2) {
            return true;
        } else {
            return false;
        }
    }
    
    protected function normalizeType($t) {
        if ($t == 'int(11)') {
            $t = 'int';
        }
        
        return $t;
    }
    
    
    public function buildCreateTable() {
        $m = $this->tableModel;
        
        $sql = 'CREATE TABLE '.$this->getTableName().' ('.PHP_EOL;
        $columns = $m->getColumns();
        foreach($columns as $c) {
            $sql .= "\t`$c`";

            $sql .= ' ' . $m->getColumnProperty( $c, 'type');
            if ($key = $m->getColumnProperty($c, 'key')) {
                $sql .= ' ' . $key;
            }
            if ($m->getColumnProperty($c, 'auto_increment')) {
                $sql .= ' AUTO_INCREMENT';
            }
            
            $sql .= ",\n";
        }
        
        $sql .= ') ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci'.PHP_EOL;
        
        return $sql;
    }
    
}


