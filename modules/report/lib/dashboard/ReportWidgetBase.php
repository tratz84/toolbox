<?php


namespace report\dashboard;


class ReportWidgetBase {
    
    protected $prio = 10;
    
    protected $reportCode;
    protected $reportTitle;
    
    
    public function __construct() {
        
    }
    
    public function setPrio($p) { $this->prio = $p; }
    public function getPrio() { return $this->prio; }
    
    public function setReportCode( $c ) { $this->reportCode = $c; }
    public function getReportCode() { return $this->reportCode; }
    
    public function setReportTitle( $t ) { $this->reportTitle = $t; }
    public function getReportTitle() { return $this->reportTitle; }
    
    
    
    
    public function render() {
        
    }
    
}



