<?php


namespace core\controller;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

abstract class BaseReportController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        $this->setShowDecorator(false);
        $this->setActionTemplate('index');
    }
    
    public abstract function report();

    
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
            
            $char .= $str[ $no2-1 ];
        }
        
        $char = $char . $str[ ($no%26) ];
        
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
        ;
    }
    
    
    public function asExcel() {
        
    }
    
}

