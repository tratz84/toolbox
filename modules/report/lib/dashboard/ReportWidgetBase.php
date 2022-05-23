<?php


namespace report\dashboard;


class ReportWidgetBase {
    
    protected $prio = 10;
    
    protected $ajaxUrl = null;
    
    protected $reportCode;
    protected $reportTitle;
    protected $reportDescription;
    
    
    public function __construct() {
        
    }
    
    public function setPrio($p) { $this->prio = $p; }
    public function getPrio() { return $this->prio; }
    
    public function setAjaxUrl( $u ) { $this->ajaxUrl = $u; }
    public function getAjaxUrl() { return $this->ajaxUrl; }
    
    public function setReportCode( $c ) { $this->reportCode = $c; }
    public function getReportCode() { return $this->reportCode; }
    
    public function setReportTitle( $t ) { $this->reportTitle = $t; }
    public function getReportTitle() { return $this->reportTitle; }
    
    public function setReportDescription( $d ) { $this->reportDescription= $d; }
    public function getReportDescription() { return $this->reportDescription; }
    
    
    public function render() {
        
    }
    
}



