<?php


namespace report\service;


use core\service\ServiceBase;
use report\model\ReportDashboard;
use report\model\ReportDashboardDAO;
use core\exception\ObjectNotFoundException;

class ReportDashboardService extends ServiceBase {
    
    
    public function readDashboard( $reportDashboardId ) {
        $rdDao = new ReportDashboardDAO();
        $rd = $rdDao->read($reportDashboardId);
        
        
        if (!$rd) {
            throw new ObjectNotFoundException( 'ReportDashboard not found' );
        }
        
        return $rd;
    }
    
    public function saveDashboard( $form ) {
        
        $id = $form->getWidgetValue( 'report_dashboard_id' );
        
        if ($id) {
            $dash = $this->readDashboard( $id );
        }
        else {
            $dash = new ReportDashboard();
        }
        
        $form->fill( $dash, array(
            'name'
        ));
        
        $grid_data = $form->getWidgetValue( 'grid_data' );
        $settings = array();
        $settings['grid_data'] = $grid_data;
        $dash->setSettings( serialize($settings) );
        
        $dash->save();
        
        return $dash;
    }
    
    
    public function readAll() {
        $rdDao = new ReportDashboardDAO();
        
        return $rdDao->readAll();
    }
    
    
    
    
    
}



