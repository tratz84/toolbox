<?php


namespace core\parser;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use core\exception\InvalidStateException;



class SheetToArray {
    
    
    
    public static function sheetToArray( $path ) {
        $i = IOFactory::identify($path);
        
        if ($i == 'Xlsx') {
            $xls = new Xlsx();
        }
        else if ($i == 'Xls') {
            $xls = new Xls();
        }
        else if ($i == 'Ods') {
            $xls = new Ods();
        }
        else if ($i == 'Csv') {
            $xls = new Csv();
        }
        else {
            throw new InvalidStateException( 'Unable to determine document type' );
        }
        
        $work = $xls->load($path);
        $sheet = $work->getActiveSheet();
        
        return $sheet->toArray();
    }
    
    
}

