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
	    $arr[] = array('menu_code' => 'dashboard',       'sort' => 100, 'visible' => 1, 'icon' => 'fa-dashboard', 'label' => 'Dashboard', 'url' => '/');
	    $arr[] = array('menu_code' => 'company',         'sort' => 200, 'visible' => 1, 'icon' => 'fa-user',      'label' => 'Bedrijven', 'url' => '/?m=base&c=company');
	    $arr[] = array('menu_code' => 'person',          'sort' => 300, 'visible' => 1, 'icon' => 'fa-user',      'label' => 'Personen',  'url' => '/?m=base&c=person');
	    if (hasCapability('invoice', 'edit-offer')) {
	        $arr[] = array('menu_code' => 'offer',           'sort' => 400, 'visible' => 1, 'icon' => 'fa-share-alt', 'label' => 'Offertes',  'url' => '/?m=invoice&c=offer');
	    }
	    if (hasCapability('invoice', 'edit-invoice')) {
    	    $arr[] = array('menu_code' => 'invoice',         'sort' => 500, 'visible' => 1, 'icon' => 'fa-file-archive-o', 'label' => strOrder(3),'url' => '/?m=invoice&c=invoice');
	    }
	    $arr[] = array('menu_code' => 'to-bill',         'sort' => 600, 'visible' => 1, 'icon' => 'fa-money', 'label' => 'Billable', 'url' => '/?m=invoice&c=tobill');
// 	    $arr[] = array('menu_code' => 'webmail',         'sort' => 700, 'visible' => 1);
	    $arr[] = array('menu_code' => 'todo',            'sort' => 800, 'visible' => 1);
	    if (hasCapability('calendar', 'edit-calendar')) {
    	    $arr[] = array('menu_code' => 'calendar',        'sort' => 900, 'visible' => 1, 'icon' => 'fa-calendar',  'label' => 'Kalender',  'url' => '/?m=calendar&c=view');
	    }
	    
	    $arr[] = array('menu_code' => 'project',         'sort' => 1000, 'visible' => 1, 'icon' => 'fa-tasks', 'label' => 'Projecten', 'url' => '/?m=project&c=project');
	    
	    $arr[] = array('menu_code' => 'rental',          'sort' => 1100, 'visible' => 1, 'icon' => 'fa-tags',      'label' => 'Plattegrond',    'url' => '/?m=rental&c=rental&a=index');
	    $arr[] = array('menu_code' => 'rentallist',      'sort' => 1200, 'visible' => 1, 'icon' => 'fa-tags',      'label' => 'Alle units',     'url' => '/?m=rental&c=rental&a=list');
	    $arr[] = array('menu_code' => 'rentalcontracts', 'sort' => 1300, 'visible' => 1, 'icon' => 'fa-tags',      'label' => 'Huurcontracten', 'url' => '/?m=rental&c=contract/view');
// 	    $arr[] = array('menu_code' => 'rentalfg',        'sort' => 1400, 'visible' => 1);
        
	    
	    if (hasCapability('webmail', 'send-mail'))
	        $arr[] = array('menu_code' => 'webmail',      'sort' => 1500, 'visible' => 1, 'icon' => 'fa-send',    'label' => 'E-mail',   'url' => '/?m=webmail&c=email');
	        
	    $arr[] = array('menu_code' => 'filesync',        'sort' => 1600, 'visible' => 1, 'icon' => 'fa-file',      'label' => 'Filesync',       'url' => '/?m=filesync&c=store');
	    
	    if (hasCapability('report', 'show-reports'))
	        $arr[] = array('menu_code' => 'report',          'sort' => 1700, 'visible' => 1, 'icon' => 'fa-signal',    'label' => 'Rapportage',     'url' => '/?m=report&c=report');
	    $arr[] = array('menu_code' => 'support',         'sort' => 1800, 'visible' => 1, 'icon' => 'fa-support', 'label' => 'Support', 'url' => '/?m=support&c=ticketList');
	    
	    if (hasCapability('base', 'edit-masterdata'))
	       $arr[] = array('menu_code' => 'masterdata',      'sort' => 1900, 'visible' => 1, 'icon' => 'fa-wrench',    'label' => 'Stamgegevens',   'url' => '/?m=base&c=masterdata/index');
	    
	    $weight = 10;
	    $menus = array();
	    foreach($arr as $a) {
	        $m = new Menu();
	        $m->setFields($a);
	        $m->setWeight($weight);
	        
	        $menus[] = $m;
	        $weight += 10;
	    }
	    
	    
	    return $menus;
	}
	

}

