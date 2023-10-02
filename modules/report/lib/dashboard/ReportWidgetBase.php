<?php


namespace report\dashboard;


use core\forms\WeekField;
use core\util\WeakMapClass;

class ReportWidgetBase extends WeakMapClass {
    
    protected $uid = null;
    
    protected $prio = 10;
    
    protected $ajaxUrl = null;
    
    protected $formClass = null;
    
    protected $reportCode;
    protected $reportTitle;
    protected $reportDescription;
    
    
    public function __construct() {
        
        $this->uid = 'report-widget-'.md5( uniqid() );
        
        
    }
    
    public function setPrio($p) { $this->prio = $p; }
    public function getPrio() { return $this->prio; }
    
    public function setAjaxUrl( $u ) { $this->ajaxUrl = $u; }
    public function getAjaxUrl() {
        if ($this->ajaxUrl)
            return $this->ajaxUrl;
        
        return appUrl( '/?m=report&c=dashboard/view&a=render_widget&code='. urlencode($this->getReportCode()) );
    }
    
    public function setFormClass( $f ) { $this->formClass = $f; }
    public function getFormClass() { return $this->formClass; }
    
    public function setReportCode( $c ) { $this->reportCode = $c; }
    public function getReportCode() { return $this->reportCode; }
    
    public function setReportTitle( $t ) { $this->reportTitle = $t; }
    public function getReportTitle() { return $this->reportTitle; }
    
    public function setReportDescription( $d ) { $this->reportDescription= $d; }
    public function getReportDescription() { return $this->reportDescription; }
    
    
    public function render() {
        
    }
    
}



