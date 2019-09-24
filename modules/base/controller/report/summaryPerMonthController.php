<?php


use core\controller\BaseReportController;
use core\container\ArrayContainer;

class summaryPerMonthController extends BaseReportController {
    
    
    public function report($render=true) {
        $datasources = new ArrayContainer();
        hook_eventbus_publish($datasources, 'base', 'report-summaryPerMonth');
        $this->datasources = $datasources->getItems();
        
        
        $this->periods = array();
        $dtStart = date('Y-m-15', strtotime('-5 years'));
        $ymNow = (int)date('Ym');
        
        while ((int)format_date($dtStart, 'Ym') <= $ymNow) {
            $this->periods[] = array(
                'month' => format_date($dtStart, 'Y-m'),
                'label' => t('month.'.format_date($dtStart, 'm')) . ' ' . format_date($dtStart, 'Y')
            );
            
            $dtStart = next_month($dtStart);
        }
        
//         $this->periods = array_reverse($this->periods);
        
        return $this->renderToString();
    }
    
}
