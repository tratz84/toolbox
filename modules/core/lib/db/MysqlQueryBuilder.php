<?php

namespace core\db;

class MysqlQueryBuilder extends QueryBuilder {
    
    protected $params = array();
    
    public function getParams() { return $this->params; }
    
    
    public function createSelect() {
        $sql = '';
        
        $fields = array();
        foreach($this->selectFields as $fn => $arr) {
            $f = '';
            if ($arr['tableName'])
                $f .= '`'.$arr['tableName'].'`.';
            $f .= '`'.$arr['field'].'`';
            
            $fields[] = $f;
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
    
    protected function buildWhere(QueryBuilderWhereContainer $c) {
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
            }
        }
        
        $r = 'WHERE (' . implode(') ' . $c->getJoinMethod() . ' (', $sql) . ')' . PHP_EOL;
        
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
    
    
    public function queryCursor(DAOObject $dao) {
        $sql = $this->createSelect();
        $params = $this->getParams();
        
        return $dao->queryCursor($sql, $params);
    }
    
    public function queryList(DAOObject $dao) {
        $sql = $this->createSelect();
        $params = $this->getParams();
        
        return $dao->queryList($sql, $params);
    }
    
    
    public function queryDelete() {
        $sql = $this->createDelete();
        
        return query($this->resourceName, $sql, $this->params);
    }
    
    public function queryInsert() {
        $sql = $this->createInsert();
        
        return query($this->resourceName, $sql, $this->params);
    }
    
    public function queryUpdate() {
        $sql = $this->createUpdate();
        
        return query($this->resourceName, $sql, $this->params);
    }
    
}


