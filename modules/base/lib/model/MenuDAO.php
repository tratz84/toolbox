<?php


namespace base\model;


class MenuDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Menu' );
	}
	
	
	public function readAll() {
// 	    return $this->queryList("select * from base__menu order by sort");
	}
	
	public function readVisible() {
	    
	    $arr = array();
	    $arr[] = array('menu_code' => 'dashboard',       'sort' => 10, 'visible' => 1, 'icon' => 'fa-dashboard', 'label' => 'Dashboard', 'url' => '/');
	    
	    $arr[] = array('menu_code' => 'todo',            'sort' => 800, 'visible' => 1);
	    
	    if (hasCapability('report', 'show-reports'))
	        $arr[] = array('menu_code' => 'report',          'sort' => 1700, 'visible' => 1, 'icon' => 'fa-signal',    'label' => 'Rapportage',     'url' => '/?m=report&c=report');
	    $arr[] = array('menu_code' => 'support',         'sort' => 1800, 'visible' => 1, 'icon' => 'fa-support', 'label' => 'Support', 'url' => '/?m=support&c=ticketList');
	    
	    if (hasCapability('base', 'edit-masterdata'))
	       $arr[] = array('menu_code' => 'masterdata',      'sort' => 99999, 'visible' => 1, 'icon' => 'fa-wrench',    'label' => t('Master data'),   'url' => '/?m=base&c=masterdata/index');
	    
	    $weight = 10;
	    $menus = array();
	    foreach($arr as $a) {
	        $m = new Menu();
	        $m->setFields($a);
	        $m->setWeight( $a['sort'] );
	        
	        if (hasCapability('core', 'userType.user') || $m->getMenuCode() == 'dashboard') {
    	        $menus[] = $m;
	        }
	        
	        $weight += 10;
	    }
	    
	    
	    return $menus;
	}
	

}

