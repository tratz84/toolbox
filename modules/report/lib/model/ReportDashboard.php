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
	
	
	public function getForms() {
	    
	    
	    
	}
	
	
}

