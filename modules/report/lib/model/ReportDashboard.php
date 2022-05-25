<?php


namespace report\model;


class ReportDashboard extends base\ReportDashboardBase {

	public function __construct($id=null) {
		parent::__construct( $id );
		
		
		$this->setActive( true );
		
	}
	
	public function getSetting( $name, $defaultValue=null ) {
	    $s = $this->getSettings();
	    
	    $s = @unserialize($s);
	    if (!$s) {
	        return $defaultValue;
	    }
	    
	    if (isset($s[$name]))
	        return $s[$name];
	    
        return $defaultValue;
	}
	
	
	public function getGridData() {
	    $d = $this->getSetting( 'grid_data', array() );
	    
        return $d;
	}
	
	public function getFormClasses() {
	    $d = $this->getSetting( 'form_classes', array() );
	    
	    return $d;
	}
	
	public function getWidgets() {
	    $allWidgets = list_report_dashboard_widgets();
	    
	    $gridData = $this->getGridData();
	    
	    $widgets = array();
	    
	    foreach($allWidgets as $w) {
	        $rc = $w->getReportCode();
	        if (isset($gridData[$rc])) {
	            $widgets[] = $w;
	        }
	    }
	    
	    return $widgets;
	}
	
	
	public function getForms() {
	    $widgets = $this->getWidgets();
	    
	    $forms = array();
	    $formClassesGenerated = array();
	    foreach($widgets as $w) {
	        $fc = $w->getFormClass();
	        
	        if (!$fc)
	            continue;
	        
            if (in_array($fc, $formClassesGenerated))
                continue;
	        
	        $form = new $fc();
	        $form->hideSubmitButtons();
	        $form->hideSubmitButtons();
	        $form->disableSubmit();
	        $forms[] = $form;
	        
	        $formClassesGenerated[] = $fc;
	    }
	    
	    // sort
	    $fcs = $this->getFormClasses();
	    usort( $forms, function($o1, $o2) use ($fcs) {
	        $p1 = pos_in_array( get_class($o1), $fcs);
	        $p2 = pos_in_array( get_class($o2), $fcs);
	        return $p1 - $p2;
	    });
	    
	    
	    
	    return $forms;
	}
}



