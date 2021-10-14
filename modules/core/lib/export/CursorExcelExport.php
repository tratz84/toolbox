<?php


namespace core\export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use core\db\DBObject;


class CursorExcelExport extends BaseExportExcel {
    
    public function export(\core\db\Cursor $cursor, $filename="file.xlsx") {
        $sheet = $this->createSheet( $cursor);
        
        $this->outputExcel($sheet, $filename);
    }
    
    public function createSheet(\core\db\Cursor $cursor) {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->setActiveSheetIndex(0);//->setCellValue('A1', 'Hello')
        
        // set header
        $headers = $this->getHeaders();
        $this->xlsHeader($sheet, $headers);
        
        
        // set content
        $x=0;
        while ($cursor->hasNext()) {
            $obj = $cursor->next();
            
            for($colno=0; $colno < count($this->fields); $colno++) {
                $f = $this->fields[$colno];
                
                if (is_a( $obj, DBObject::class )) {
                    $val = $obj->getterOrField( $f['name'] );
                }
                
                // callback?
                $val = $this->callCallbackColumnValue( $f['name'], $val, [ 'row' => $obj ]);
                
                
                $this->xlsCol($sheet, $x+2, $colno+1, $val, isset($f['type'])?$f['type']:'text');
            }
            
            $x++;
        }
        
        return $spreadsheet;
    }
}

