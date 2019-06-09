<?php

namespace core\db\query;


class QueryBuilderWhere {
    
    protected $left;
    protected $leftIsValue = true;             // is value (auto-escaped)? or is tablecolumn-reference?
    
    protected $comparisonMethod = '=';
    
    protected $right;
    protected $rightIsValue = true;            // is value (auto-escaped)? or is tablecolumn reference?
    
    
    
    public function __construct() {
        
    }
    
    
    public static function whereValByVal($left, $comparison, $right) {
        $w = new QueryBuilderWhere();
        $w->setLeft($left, true);
        $w->setRight($right, true);
        $w->setComparisonMethod($comparison);
        
        return $w;
    }
    public static function whereValByRef($left, $comparison, $right) {
        $w = new QueryBuilderWhere();
        $w->setLeft($left, true);
        $w->setRight($right, false);
        $w->setComparisonMethod($comparison);
        
        return $w;
    }
    
    public static function whereRefByRef($left, $comparison, $right) {
        $w = new QueryBuilderWhere();
        $w->setLeft($left, false);
        $w->setRight($right, false);
        $w->setComparisonMethod($comparison);
        
        return $w;
    }
    public static function whereRefByVal($left, $comparison, $right) {
        $w = new QueryBuilderWhere();
        $w->setLeft($left, false);
        $w->setRight($right, true);
        $w->setComparisonMethod($comparison);
        
        return $w;
    }
    
    
    
    public function getComparisonMethod() { return $this->comparisonMethod; }
    public function setComparisonMethod($m) {
        $m = strtoupper($m);
        
        // TODO: examinate possibilities
        //         if (in_array($m, ['IN', '=', '<>', '!=', '>', '>=', '<', '<='])) {
        //             throw new InvalidStateException('Unknown comparison method: '.$m);
        //         }
        
        $this->comparisonMethod = $m;
    }
    
    public function getLeft() { return $this->left; }
    public function leftIsValue() { return $this->leftIsValue; }
    
    public function setLeft($v, $isValue=true) {
        $this->left = $v;
        $this->leftIsValue = $isValue ? true : false;
    }
    public function setLeftIsValue($bln) { $this->leftIsValue = $bln; }

    
    public function getRight() { return $this->right; }
    public function rightIsValue() { return $this->rightIsValue; }
    
    public function setRight($v, $isValue=true) {
        $this->right = $v;
        $this->rightIsValue = $isValue ? true : false;
    }
    public function setRightIsValue($bln) { $this->rightIsValue = $bln; }
    
    
}


