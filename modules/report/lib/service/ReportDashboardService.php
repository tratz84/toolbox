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
            'active',
            'name'
        ));
        
        // decode
        $fs = $form->getWidgetValue( 'settings' );
        $fs = json_decode( $fs, true );
        
        // format
        $settings = array();
        $settings['grid_data']    = $fs['grid_data'];
        $settings['form_classes'] = $fs['form_classes'];
        
        $dash->setSettings( serialize($settings) );
        
        $dash->save();
        
        return $dash;
    }
    
    
    
    public function readDashboards() {
        $rdDao = new ReportDashboardDAO();
        
        if (hasCapability('report', 'edit-dashboard')) {
            return $rdDao->readAll();
        }
        else {
            return $rdDao->readActive();
        }
    }
    
    
    public function deleteDashboard( $reportDashboardId ) {
        
        $d = $this->readDashboard($reportDashboardId);
        
        
        // TODO: log?
        
        return $d->delete();
        
    }
    
    
    
    
}



