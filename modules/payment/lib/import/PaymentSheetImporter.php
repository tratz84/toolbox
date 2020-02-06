<?php

namespace payment\import;


use core\parser\SheetReader;
use core\exception\InvalidStateException;

class PaymentSheetImporter {
    
    protected $sheetFile = null;
    
    protected $mapping = null;
    
    protected $rows = null;
    
    
    public function __construct() {
        
    }
    
    
    public function setSheetFile($f) { $this->sheetFile = $f; }
    public function setMapping($m) { $this->mapping = $m; }
    
    public function parseSheet() {
        $sr = new SheetReader( $this->sheetFile );
        $sr->read();
        
        $this->rows = $sr->getRows();
    }
    
    protected function getValue($row, $name, $defaultValue=null) {
        if ($this->mapping == null) {
            throw new InvalidStateException('No mapping set');
        }
        
        if (isset($this->mapping[$name])) {
            $colno = $this->mapping[$name];
            
            if (is_numeric($colno) && $colno >= 0 && $colno < count($row)) {
                return $row[$colno];
            } else {
                return $defaultValue;
            }
        } else {
            return $defaultValue;
        }
    }
    
    protected function normalizeRow(&$data) {
        
        if ($data['debet_credit']) {
            if ($data['debet_credit'] == 'Bij') $data['debet_credit'] = 'D';
            if ($data['debet_credit'] == 'Af')  $data['debet_credit'] = 'C';
        }
        
        if ($data['amount']) {
            $data['amount'] = str_replace(',', '.', $data['amount']);
            $data['amount'] = strtodouble($data['amount']);
        }
        
        
        if (is_numeric($data['amount']) && $data['amount'] > 0 && $data['debet_credit'] == 'C') {
            $data['amount'] = $data['amount'] * -1;
        }
        
        if ($data['bankaccountno_contra'] == null) {
            $matches = array();
            if (preg_match('/IBAN: (\\S+)/', $data['description'], $matches)) {
                $data['bankaccountno_contra'] = $matches[1];
            }
        }
        
        if (preg_match('/^\\d{8}$/', $data['payment_date'])) {
            $year = (int)substr($data['payment_date'], 0, 4);
            if ($year > date('Y')-100 && $year < date('Y')+100) {
                $data['payment_date'] = $year . '-' . substr($data['payment_date'], 4, 2) . '-' . substr($data['payment_date'], 6, 2);
            }
        }
        
    }
    
    
    public function parseRow($rowNo) {
        if ($rowNo == 0) {
            throw new InvalidStateException('First row not parsable');
        }
        
        if ($rowNo < 0 || $rowNo > count($this->rows)-1) {
            throw new InvalidStateException('Invalid row');
        }
        
        $row = $this->rows[$rowNo];        
        
        // map row to key-value array
        $m = array();
        $m['debet_credit']         = $this->getValue($row, 'debet_credit');
        $m['amount']               = $this->getValue($row, 'amount');
        $m['bankaccountno']        = $this->getValue($row, 'bankaccountno');
        $m['bankaccountno_contra'] = $this->getValue($row, 'bankaccountno_contra');
        $m['payment_date']         = $this->getValue($row, 'payment_date');
        $m['name']                 = $this->getValue($row, 'name');
        $m['description']          = $this->getValue($row, 'description');
        $m['code']                 = $this->getValue($row, 'code');
        $m['mutation_type']        = $this->getValue($row, 'mutation_type');
        
        $this->normalizeRow( $m );
//         var_export($m);exit;
        
        return $m;
    }
    
    
    
    
    
}

