<?php

namespace core\db\query;

use core\exception\InvalidStateException;
use core\exception\QueryException;

class MysqlQueryBuilder extends QueryBuilder {
    
    protected $params = array();
    
    protected $selectFunctions = array();
    
    public function getParams() { return $this->params; }
    
    
    /**
     * setFunctions() - set select functions, possible methods,
     *      setFields(array('sum(col1)'))
     *      setFields('sum(col1)')
     */
    public function selectFunctions() {
        $arr = func_get_args();
        foreach($arr as $a) {
            if (is_array($a) == false)
                $a = array( $a );
                
            foreach($a as $func) {
                $this->selectFunction($func);
            }
        }
        return $this;
    }
    public function getSelectFunctions() { return $this->selectFunctions; }
    public function selectFunction($func) {
        $this->selectFunctions[] = $func;
        return $this;
    }
    public function clearSelectFunctions() { $this->selectFunctions= array(); }
    
    
    
    public function createSelect() {
        $sql = '';
        
        $fields = array();
        foreach($this->selectFields as $fn => $arr) {
            $f = '';
            if ($arr['tableName'])
                $f .= '`'.$arr['tableName'].'`.';
            
            if ($arr['field'] == '*') {
                $f .= '*';
            } else if (strpos($arr['field'], "'") === 0 && endsWith($arr['field'], "'") == true) {
                $f .= $arr['field'];
            } else {
                $f .= '`'.$arr['field'].'`';
            }
            
            if ($arr['label'])
                $f .= ' ' . $arr['label'];
            
            $fields[] = $f;
        }
        
        // add functions
        $fields = array_merge($fields, $this->selectFunctions);
        
        
        if (count($fields) == 0) {
            $fields[] = '*';
        }
        
        if (!$this->table) {
            throw new QueryException('No table selected');
        }
        
        
        $sql = 'SELECT ' . implode(', ', $fields) . PHP_EOL;
        $sql .= 'FROM `'.$this->table.'`' . PHP_EOL;
        
        foreach($this->join as $j) {
            $sql .= 'JOIN `'.$j['table'].'` ON (`'.$j['table'].'`.`'.$j['fieldJoinTable'].'` = `' . $j['parentTable'].'`.`'.$j['fieldParentTable'].'`)' . PHP_EOL; 
        }
        foreach($this->leftJoin as $j) {
            $sql .= 'LEFT JOIN `'.$j['table'].'` ON (`'.$j['table'].'`.`'.$j['fieldJoinTable'].'` = `' . $j['parentTable'].'`.`'.$j['fieldParentTable'].'`)' . PHP_EOL;
        }
        foreach($this->rightJoin as $j) {
            $sql .= 'RIGHT JOIN `'.$j['table'].'` ON (`'.$j['table'].'`.`'.$j['fieldJoinTable'].'` = `' . $j['parentTable'].'`.`'.$j['fieldParentTable'].'`)' . PHP_EOL;
        }
        
//         $this->whereContainer
        $sql .= $this->buildWhere($this->whereContainer);
        
        if ($this->groupBy) {
            $sql .= 'GROUP BY ' . $this->groupBy . PHP_EOL;
        }
        
        if ($this->orderBy) {
            // TODO: filter orderBy to prevent injections
            $sql .= 'ORDER BY '.$this->orderBy . PHP_EOL;
        }
        
        if (!$this->start && $this->limit > 0) {
            $sql .= 'LIMIT ' . intval($this->limit) . PHP_EOL;
        } else if ($this->start && $this->limit > 0) {
            $sql .= 'LIMIT ' . intval($this->start) . ', ' . intval($this->limit) . PHP_EOL;
        }
        
        return $sql;
    }
    
    public function createUpdate() {
        $sql = 'UPDATE `'.$this->table.'`' . PHP_EOL;
        
        $fields = array();
        foreach($this->fieldValues as $fieldName => $val) {

            if ($val === null) {
                $fields[] = '`'.$fieldName.'` = NULL';
            } else if (is_bool($val)) {
                $fields[] = '`'.$fieldName.'` = ' . ($val?'true':'false');
            } else {
                $fields[] = '`'.$fieldName.'` = ?';
                $this->params[] = $val;
            }
        }
        
        $sql .= 'SET ' . implode(', ', $fields) . PHP_EOL;
        
        $sql .= $this->buildWhere($this->whereContainer);
        
        return $sql;
    }
    
    public function createInsert() {
        $this->params = array();
        $marks = array();
        $fields = array();
        
        foreach($this->fieldValues as $fieldName => $val) {
            $fields[] = '`'.$fieldName.'`';
            
            if ($val === null) {
                $marks[] = 'NULL';
            } else if (is_bool($val)) {
                $marks[] = $val ? 'true' : 'false';
            } else {
                $this->params[] = $val;
                $marks[] = '?';
            }
        }
        
        $sql = 'INSERT INTO `'.$this->table.'`' . PHP_EOL;
        $sql .= '(' . implode(', ', $fields) . ') ';
        $sql .= ' VALUES ';
        $sql .= '(' . implode(', ', $marks) . ')';
        
        return $sql;
    }
    
    public function createDelete() {
        $sql = 'DELETE FROM `'.$this->table.'`' . PHP_EOL;
        
        $sql .= $this->buildWhere($this->whereContainer);
        
        return $sql;
    }
    
    protected function buildWhere(QueryBuilderWhereContainer $c, $includeWhere=true) {
        $sql = array();
        
        $where = $c->getWhere();
        
        // no where-clauses?
        if (count($where) == 0)
            return '';
        
        foreach($where as $w) {
            if (is_a($w, QueryBuilderWhere::class)) {
                $str = '';
                
                if ($w->leftIsValue()) {
                    $v = $w->getLeft();
                    
                    if ($v === null || is_bool($v)) {
                        $str .= $this->sqlVal($v);
                    } else {
                        $this->params[] = $v;
                        $str .= ' ? ';
                    }
                } else {
                    $str .= $this->sqlVal( $w->getLeft() );
                }
                
                $str .= ' ' . $w->getComparisonMethod() . ' ';
                
                if ($w->rightIsValue()) {
                    $v = $w->getRight();
                    if ($v === null || is_bool($v)) {
                        $str .= $this->sqlVal($v);
                    } else {
                        $this->params[] = $v;
                        $str .= ' ? ';
                    }
                } else {
                    $str .= $this->sqlVal( $w->getRight() );
                }
                
                $sql[] = $str;
            } else if (is_a($w, QueryBuilderWhereContainer::class)) {
                
                $sql[] = $this->buildWhere($w, false);
                
            } else {
                throw new InvalidStateException('Invalid where class given');
            }
        }
        
        $r = '';
        if ($includeWhere) {
            $r .= 'WHERE ';
        }
        
        $r .= '(' . implode(') ' . $c->getJoinMethod() . ' (', $sql) . ')' . PHP_EOL;
        
        return $r;
    }
    
    protected function sqlVal($v) {
        if (is_bool($v)) {
            if ($v === false) {
                return 'false';
            } else {
                return 'true';
            }
        }
        if ($v === null) {
            return 'NULL';
        }
        
        return $v;
    }
    
    
    public function queryCursor($objectName=null) {
        if ($objectName == null)
            $objectName = $this->getObjectName();
        
        $sql = $this->createSelect();
        $params = $this->getParams();
        
        $cursor = $this->dbconnection->queryCursor($objectName, $sql, $params);
        
        return $cursor;
    }
    
    public function queryList($objectName=null) {
        if ($objectName == null)
            $objectName = $this->getObjectName();
        
        $sql = $this->createSelect();
        $params = $this->getParams();
        
        $list = array();
        
        $rows = $this->dbconnection->queryList($sql, $params);
        foreach($rows as $r) {
            $obj = new $objectName();
            $obj->setFields($r);
            
            $list[] = $obj;
        }
        
        return $list;
    }
    
    public function queryOne($objectName=null) {
        if ($objectName == null)
            $objectName = $this->getObjectName();
        
        $sql = $this->createSelect();
        $params = $this->getParams();
        
        $res = $this->dbconnection->query($sql, $params);
        
        $row = $res->fetch_assoc();
        if ($row) {
            $obj = new $objectName();
            $obj->setFields($row);
            
            return $obj;
        }
        
        
        return null;
    }
    
    
    public function queryDelete() {
        $sql = $this->createDelete();
        
        return $this->dbconnection->query($sql, $this->params);
    }
    
    public function queryInsert() {
        $sql = $this->createInsert();
        
        return $this->dbconnection->query($sql, $this->params);
    }
    
    public function queryUpdate() {
        $sql = $this->createUpdate();
        
        return $this->dbconnection->query($sql, $this->params);
    }
    
}


