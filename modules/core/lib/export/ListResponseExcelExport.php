<?php


namespace core\export;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use core\forms\lists\ListResponse;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ListResponseExcelExport {
    
    protected $fields;
    
    
    public function __construct($fields=array()) {
        
        $this->fields = $fields;
    }
    
    
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
                
                $val = $objs[$x][ $f['name'] ];
                
                $this->xlsCol($sheet, $x+2, $colno+1, $val, isset($f['type'])?$f['type']:'text');
            }
        }
        
        return $spreadsheet;
    }
    
    public function getHeaders() {
        $headers = array();
        
        foreach($this->fields as $f) {
            $headers[] = $f['label'];
        }
        
        return $headers;
    }
    
    
    
    public function xlsHeader($sheet, $headers, $rowno=1) {
        for($x=0; $x < count($headers); $x++) {
            $sheet->setCellValue($this->colCode($rowno, $x+1), $headers[$x]);
        }
    }
    
    public function xlsCol($sheet, $rowno, $colno, $val, $field='text') {
        
        switch($field) {
            case 'datetime' :
                if (valid_datetime($val)) {
                    $val = Date::PHPToExcel($val);
                    $sheet->setCellValue($this->colCode($rowno, $colno), $val);
                    
                    $sheet->getStyle($this->colCode($rowno, $colno))->getNumberFormat()->setFormatCode('dd-mm-yyyy hh:mm:ss');
                }
                break;
            case 'date' :
                if (valid_date($val)) {
                    $val = Date::PHPToExcel($val);
                    $sheet->setCellValue($this->colCode($rowno, $colno), $val);
                    
                    $sheet->getStyle($this->colCode($rowno, $colno))->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                }
                break;
            case 'bool' :
            case 'boolean' :
                $sheet->setCellValueExplicit($this->colCode($rowno, $colno), $val ? true : false, DataType::TYPE_BOOL);
                
                break;
            case 'numeric' :
                $sheet->setCellValueExplicit($this->colCode($rowno, $colno), $val, DataType::TYPE_NUMERIC);
                
                break;
            case 'formula' :
                $sheet->setCellValueExplicit($this->colCode($rowno, $colno), $val, DataType::TYPE_FORMULA);
                
                break;
            default :
                $sheet->setCellValue($this->colCode($rowno, $colno), $val);
        }
    }
    
    
    public function colCode($rowno, $colno) {
        return $this->colChar($colno).$rowno;
    }
    
    public function colChar($no) {
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        $no = $no-1;
        
        $char = '';
        
        if ($no >= strlen($str)) {
            $no2 = ($no-($no%strlen($str))) / strlen($str);
            
            $char .= $str{$no2-1};
        }
        
        $char = $char . $str{($no%26)};
        
        return $char;
        
    }
    
    public function outputExcel(Spreadsheet $spreadsheet, $filename) {
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    
}
