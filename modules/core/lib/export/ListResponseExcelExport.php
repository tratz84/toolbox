<?php


namespace core\export;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use core\forms\lists\ListResponse;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use core\db\DBObject;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ListResponseExcelExport extends BaseExportExcel {
    
    
    public function export(ListResponse $response, $filename="file.xlsx") {
        $sheet = $this->createSheet($response);
        
        $this->outputExcel($sheet, $filename);
    }
    
    public function createSheet(ListResponse $response) {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->setActiveSheetIndex(0);//->setCellValue('A1', 'Hello')
        
        // set header
        $headers = $this->getHeaders();
        $this->xlsHeader($sheet, $headers);
        
        
        // set content
        $objs = $response->getObjects();
        for($x=0; $x < count($objs); $x++) {
            for($colno=0; $colno < count($this->fields); $colno++) {
                $f = $this->fields[$colno];
                
                if (is_a( $objs[$x], DBObject::class )) {
                    $val = $objs[$x]->getterOrField( $f['name'] );
                }
                else {
                    $val = $objs[$x][ $f['name'] ];
                }
                
                // callback?
                $val = $this->callCallbackColumnValue( $f['name'], $val, [ 'row' => $objs[$x] ]);
                
                $this->xlsCol($sheet, $x+2, $colno+1, $val, isset($f['type'])?$f['type']:'text');
            }
        }
        
        return $spreadsheet;
    }
}
