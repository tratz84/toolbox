<?php

namespace core\parser;

class SheetReader {
    
    protected $file = null;
    protected $rows = null;
    
    public function __construct($file) {
        $this->file = $file;
        
    }
    
    
    public function getRowCount() {
        if (is_array($this->rows)) {
            return count( $this->rows );
        } else {
            return -1;
        }
    }
    
    public function getRows() { return $this->rows; }
    
    public function getRow($no) {
        if ($no < 0 || $no > $this->getRowCount()-1) {
            return null;
        }
        
        return $this->rows[$no];
    }
    
    
    public function read() {
        $ext = file_extension( $this->file );
        
        if ($ext == 'csv') {
            return $this->readCsv();
        }
        else if ($ext == 'xls' || $ext == 'xlsx') {
            $this->readXls();
        }
        else {
            return false;
        }
    }
    
    protected function readCsv() {
        $fh = fopen($this->file, 'r');
        if (!$fh)
            return false;
        
        $this->rows = array();
        while($r = fgetcsv($fh)) {
            $this->rows[] = $r;
        }
        
        fclose($fh);
        
        // TODO: support semi-colon files?
        // TODO: check if all rows have same column-count?
        // TODO: fix if not ?
        
        if (count($this->rows) > 0) {
            return true;
        }
        
        $this->rows = null;
        
        return false;
    }
    
    protected function readXls() {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile( $this->file );
        $reader->setReadDataOnly( true );
        
        /** @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet */
        $doc = $reader->load( $this->file );
        $sheetCount = $doc->getSheetCount();
        
        // no sheets?
        if ($sheetCount < 1) {
            return false;
        }
        
        /** @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet */
        $sheet = $doc->getSheet(0);
        
        // BOEM :)
        $this->rows = $sheet->toArray();
    }
    
}


