<?php



namespace reportbuilder\export;


use reportbuilder\db\ReportQuery;
use core\export\ListResponseExcelExport;

class ExcelExport {
    
    /**
     * @var ReportQuery
     */
    protected $report;
    
    protected $filename = 'export.xlsx';
    
    
    public function __construct( $report ) {
        $this->report = $report;
    }
    
    
    
    public function setFilename($f) { $this->filename = $f; }
    public function getFilename() { return $this->filename; }
    
    
    
    public function export() {
        // determine filename
        $f = $this->getFilename();
        if (strpos($f, '.xls') === false) {
            $f = $f . '.xlsx';
        }
        
        // fetch export data
        $lr = $this->report->createListResponse();
        
        // fetch report fields
        $fields = $this->report->getReportFields();
        
        // ListResponse-mapping compatible fieldlist
        $lrFields = array();
        foreach($fields as $k => $v) {
            $lrField = array();
            $lrField['name'] = $k;
            $lrField['label'] = $v['fieldDescription'];
            $lrField['type'] = $v['fieldType'];
            
            $lrFields[] = $lrField;
        }
        
        // export
        $ee = new ListResponseExcelExport( $lrFields );
        $ee->export( $lr, $f );
    }
    
    
    
}



