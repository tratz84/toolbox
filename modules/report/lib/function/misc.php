<?php

use core\container\ArrayContainer;



function list_report_dashboard_widgets() {
    $ac = new ArrayContainer();
    
    hook_eventbus_publish( $ac, 'report', 'dashboard-widgets' );
    
    $items = $ac->getItems();
    
    usort($items, function($o1, $o2) {
        $p1 = $o1->getPrio();
        $p2 = $o2->getPrio();
        
        if ($p1 != $p2) {
            return $p2 = $p1;
        }
        
        return strcmp( $o1->getReportTitle(), $o2->getReportTitle() );
    });
    
    return $items;
}



