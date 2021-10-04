<?php


namespace reportbuilder\controller;



use reportbuilder\db\ReportQuery;

class ReportBuilderController extends \core\controller\BaseReportController {
    
    
    /**
     * @var ReportQuery
     */
    protected $report = null;
    
    
    public function report() {
        if ($this->report->getForm()) {
            $this->report->getForm()->bind( $_REQUEST );
        }
        
        $this->it = $this->report->createIndexTable();
        
        $arr = $_REQUEST;
        unset($arr['object-locked']);
        unset($arr['form-name']);
        unset($arr['default-button']);
        
        // set current module & controller vars
        $arr['m'] = $this->getModuleName();
        $arr['c'] = $this->getControllerPath();
        $arr['a'] = 'search';
        
        $connectorUrl = http_build_query( $arr );
        
        $this->it->setConnectorUrl( '/?'.$connectorUrl );
    }
    
    
    public function renderToString() {
        
        $this->form = $this->report->getForm();
        
        return parent::renderToString();
    }
    
    
    public function action_search() {
        $arr = array();
        $arr['listResponse'] = $this->report->createListResponse();
        
        $this->json($arr);
    }
    
    
    public function action_xls() {
        $exp = $this->report->createExcelExport();
        
        if ($this->report->getExcelFilename()) {
            $exp->setFilename( $this->report->getExcelFilename() );
        }
        else {
            $exp->setFilename( $this->report->getReportName() . ' - ' . date('Y-m-d').'.xlsx' );
        }
        $exp->export();
        
    }
    
    
    
}




