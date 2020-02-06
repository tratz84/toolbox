<?php

use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\parser\SheetReader;
use payment\form\PaymentImportMappingForm;
use payment\import\PaymentSheetImporter;

class sheetController extends BaseController {
    
    
    public function action_index() {
        $f = basename(get_var('f'));
        
        $fullpath = get_data_file_safe('/tmp/', $f);
        if ($fullpath == false) {
            throw new InvalidStateException('Sheet file not found');
        }
        
        $sr = new SheetReader( $fullpath );
        if ($sr->read()) {
            $head = $sr->getRow(0);
            implode(',', $head);
            
            $this->form = new PaymentImportMappingForm();
            $this->form->setImportHeaders( $head );
            
            $this->tmpfile = basename($fullpath);
        } else {
            $this->error = 'Unable to read sheet';
        }
        
        return $this->render();
    }
    
    public function action_import_sample() {
        if (isset($this->sheet_file) == false)
            return;
        
        $f = basename($this->sheet_file);
        
        $fullpath = get_data_file_safe('/tmp/', $f);
        if ($fullpath == false) {
            throw new InvalidStateException('Sheet file not found');
        }
        

        $sr = new SheetReader( $fullpath );
        if ($sr->read()) {
            $this->sheet = $sr;
            
            $this->setShowDecorator(false);
            $this->render();
        }
    }
    
    
    public function action_sample_data() {
        $file = get_var('sheet_file');
        
        if (!$file) {
            return $this->json(array('error' => true, 'message' => 'No sheet file given'));
        }
        
        $file = basename($file);
        
        $fullpath = get_data_file_safe('/tmp/', $file);
        if ($fullpath == false) {
            throw new InvalidStateException('Sheet file not found');
        }
        
        $mapping = array();
        $chars_skip = strlen('col-');
        $mapping['debet_credit']         = substr($_POST['debet_credit'],         $chars_skip);
        $mapping['amount']               = substr($_POST['amount'],               $chars_skip);
        $mapping['bankaccountno']        = substr($_POST['bankaccountno'],        $chars_skip);
        $mapping['bankaccountno_contra'] = substr($_POST['bankaccountno_contra'], $chars_skip);
        $mapping['payment_date']         = substr($_POST['payment_date'],         $chars_skip);
        $mapping['name']                 = substr($_POST['name'],                 $chars_skip);
        $mapping['description']          = substr($_POST['description'],          $chars_skip);
        $mapping['code']                 = substr($_POST['code'],                 $chars_skip);
        $mapping['mutation_type']        = substr($_POST['mutation_type'],        $chars_skip);
        
        
        $psi = new PaymentSheetImporter();
        $psi->setSheetFile($fullpath);
        $psi->setMapping( $mapping );
        
        
    }
    
    
}



