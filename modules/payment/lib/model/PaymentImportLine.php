<?php


namespace payment\model;


class PaymentImportLine extends base\PaymentImportLineBase {

    protected static $lineStatuses = array(
        'unknown',              // not linked to a customer/invoice
        'skip',                 // skipped
        'ready',                // ready to import
        'imported'              // imported
    );


    public function generateTransactionId() {
        
        $str = '';
        $str .= $this->getPaymentDate();
        $str .= $this->getDescription();
        $str .= $this->getBankaccountnoContra();
        $str .= $this->getAmount();
        
        $trxid = crc32_int32( $str );
        
        $this->setTransactionId($trxid);
    }
    
    
    public function getImportStatus() {
        $p = parent::getImportStatus();
        
        if ($p == 'skip' || $p == 'imported') {
            return $p;
        }
        
        if ($this->getCompanyId() || $this->getPersonId()) {
            return 'ready';
        }
        
        return 'unknown';
    }
    
    
}

