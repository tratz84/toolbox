<?php

namespace payment\service;


use core\service\ServiceBase;
use payment\model\PaymentImport;
use payment\model\PaymentImportLine;
use payment\import\PaymentSheetImporter;

class PaymentImportService extends ServiceBase {
    
    
    public function stageImport($file, $mapping) {
        
        $psi = new PaymentSheetImporter();
        $psi->setSheetFile( $file );
        $psi->setMapping( $mapping );
        $psi->parseSheet();
        
        $pi = new PaymentImport();
        $pi->setDescription('New import ' . basename($file));
        $pi->save();
        
        $pils = array();
        for($x=1; $x < $psi->getRowCount(); $x++) {
            $pil = $psi->createPaymentImportLine( $x );
            $pil->setPaymentImportId( $pi->getPaymentImportId() );
            
            $pil->save();
            
            $pi->addImportLine( $pil );
        }
        
        return $pi;
    }
    
    
}

