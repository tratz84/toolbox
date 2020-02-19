<?php

namespace payment\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use payment\import\PaymentSheetImporter;
use payment\model\PaymentImport;
use payment\model\PaymentImportDAO;
use payment\model\PaymentImportLineDAO;
use core\exception\ObjectNotFoundException;
use invoice\service\InvoiceService;

class PaymentImportService extends ServiceBase {
    
    
    public function stageImport($file, $mapping) {
        
        $psi = new PaymentSheetImporter();
        $psi->setSheetFile( $file );
        $psi->setMapping( $mapping );
        $psi->parseSheet();
        
        $pi = new PaymentImport();
        $pi->setDescription('New import ' . basename($file));
        $pi->save();
        
        for($x=1; $x < $psi->getRowCount(); $x++) {
            $pil = $psi->createPaymentImportLine( $x );
            $pil->setPaymentImportId( $pi->getPaymentImportId() );
            
            $pil->save();
            
            $pi->addImportLine( $pil );
        }
        
        return $pi;
    }
    
    public function deleteImport($paymentImportId) {
        // throws ObjectNotFound-exception if not found
        $pi = $this->readImport( $paymentImportId );
        
        
        // delete
        $piDao = object_container_get(PaymentImportDAO::class);
        $pilDao = object_container_get(PaymentImportLineDAO::class);
        
        $pilDao->deleteByImport( $paymentImportId );
        $piDao->delete( $paymentImportId );
    }
    
    
    public function readImport($id) {
        $piDao = object_container_get(PaymentImportDAO::class);
        $pi = $piDao->read($id);
        
        if (!$pi) {
            throw new ObjectNotFoundException('PaymentImport not found');
        }
        
        $pilDao = object_container_get(PaymentImportLineDAO::class);
        $lines = $pilDao->readByImport( $pi->getPaymentImportId() );
        
        $pi->setImportLines($lines);
        
        return $pi;
    }
    
    
    public function searchImport($start, $limit, $opts = array()) {
        $piDao = new PaymentImportDAO();
        
        $cursor = $piDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('payment_import_id', 'description', 'created', 'count'));
        
        return $r;
    }
    
    
    public function setCustomer($paymentImportLineId, $companyId, $personId) {
        $pilDao = new PaymentImportLineDAO();
        
        $pil = $pilDao->read( $paymentImportLineId );
        
        if (!$pil) {
            throw new ObjectNotFoundException('PaymentImportLine not found');
        }
        
        $pil->setPersonId( null );
        $pil->setCompanyId( null );
        if ($companyId) {
            $pil->setCompanyId( $companyId );
        } else if ($personId) {
            $pil->setPersonId( $personId );
        }
        
        return $pil->save();
    }
    
    
    public function setInvoice($paymentImportLineId, $invoiceId) {
        // fetch PaymentImportLine
        $pilDao = new PaymentImportLineDAO();
        
        $pil = $pilDao->read( $paymentImportLineId );
        if (!$pil) {
            throw new ObjectNotFoundException('PaymentImportLine not found');
        }
        
        // fetch Invoice
        if ($invoiceId != null) {
            $invoiceService = object_container_get(InvoiceService::class);
            $invoice = $invoiceService->readInvoice( $invoiceId );
            
            if (!$invoice) {
                throw new ObjectNotFoundException('Invoice not found');
            }
            $pil->setInvoiceId( $invoice->getInvoiceId() );
        } else {
            $pil->setInvoiceId( null );
        }
        
        
        
        return $pil->save();
    }
    
    
    
    
    
}

