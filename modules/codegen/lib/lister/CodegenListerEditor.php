<?php

namespace codegen\lister;


class CodegenListerEditor {
    
    protected $listerClass;
    protected $daoClass;
    protected $query;
    
    protected $sortable = false;
    
    protected $htmlFields = array();
    protected $exportFields = array();
    protected $searchFields = array();
    
    
    public function setListerClass($c) { $this->listerClass = $c; }
    public function getListerClass() { return $this->listerClass; }
    
    public function setDaoClass($c) { $this->daoClass = $c; }
    public function getDaoClass() { return $this->daoClass; }
    
    public function setQuery($q) { $this->query = $q; }
    public function getQuery() { return $this->query; }
    
    public function enableSortable() { $this->sortable = true; }
    public function disableSortable() { $this->sortable = true; }
    public function setSortable($bln) { $this->sortable = $bln ? true : false; }
    public function isSortable() { return $this->sortable ? true : false; }
    
    public function addHtmlField($htmlField) { $this->htmlFields[] = $htmlField; }
    public function getHtmlFields() { return $this->htmlFields; }
    
    public function addExportField($exportField) { $this->exportFields[] = $exportField; }
    public function getExportFields() { return $this->exportFields; }

    public function addSearchField($searchField) { $this->searchFields[] = $searchField; }
    public function getSearchFields() { return $this->searchFields; }
    
    
    
    public function read() {
        
    }
    
    
    public function generate() {
        
    }
    
    
    
    
}
