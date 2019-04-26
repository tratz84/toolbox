<?php

namespace report;

class ReportMenuItem {
    
    protected $module;
    protected $name;
    protected $controllerName;
    protected $excelUrl;
    
    
    public function __construct($module, $name, $controllerName, $excelUrl=null) {
        $this->setModule($module);
        $this->setName($name);
        $this->setControllerName($controllerName);
        $this->setExcelUrl($excelUrl);
    }
    
    public function getModule() { return $this->module; }
    public function setModule($p) { $this->module = $p; }
    
    public function getName() { return $this->name; }
    public function setName($p) { $this->name = $p; }
    
    public function getControllerName() { return $this->controllerName; }
    public function setControllerName($p) { $this->controllerName = $p; }
    
    public function getExcelUrl() { return $this->excelUrl; }
    public function setExcelUrl($p) { $this->excelUrl = $p; }
    
}

