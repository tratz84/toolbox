<?php

namespace core\db;

class MysqlQueryBuilder extends QueryBuilder {
    
    protected $params = array();
    
    
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
        
        
        return $sql;
    }
    
    public function createUpdate() {
        $sql = 'UPDATE `'.$this->table.'`' . PHP_EOL;
        
        $fields = array();
        foreach($this->updateFields as $fieldName => $val) {
            $f = '`'.$fieldName.'` = ?';
            
            $fields[] = $f;
            $this->params[] = $val;
        }
        
        $sql .= 'SET ' . implode(', ', $fields) . PHP_EOL;
        
        $sql .= $this->buildWhere($this->whereContainer);
        
        return $sql;
    }
    
    protected function buildWhere(QueryBuilderWhereContainer $c) {
        
        $sql = array();
        
        $where = $c->getWhere();
        foreach($where as $w) {
            if (is_a($w, QueryBuilderWhere::class)) {
                $str = '';
                
                if ($w->leftIsValue()) {
                    $this->params[] = $w->getLeft();
                    $str .= ' ? ';
                } else {
                    $str .= $this->sqlVal( $w->getLeft() );
                }
                
                $str .= ' ' . $w->getComparisonMethod() . ' ';
                
                if ($w->rightIsValue()) {
                    $this->params[] = $w->getRight();
                    $str .= ' ? ';
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
        
        return $v;
    }
    
    
    public function getParams() { return $this->params; }
    
    public function queryUpdate() {
        $sql = $this->createUpdate();
        
        return query($this->resourceName, $sql, $this->params);
    }
    
}


