<?php


namespace payment\model;


use core\exception\InvalidStateException;

class PaymentImportLine extends base\PaymentImportLineBase {

    protected static $lineStatuses = array(
        'unknown',              // not linked to a customer/invoice
        'skip',                 // skipped
        'duplicate',
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
        
        if ($p == 'skip' || $p == 'imported' || $p == 'duplicate') {
            return $p;
        }
        
        if ($this->getCompanyId() || $this->getPersonId()) {
            return 'ready';
        }
        
        return 'unknown';
    }
    
    public function setImportStatus($s) {
        if ($s != 'open' && in_array($s, self::$lineStatuses) == false) {
            throw new InvalidStateException('PaymentImportLine status unknown');
        }
        
        return parent::setImportStatus($s);
    }
    
    
    
    public function asArray() {
        $l = array();
        
        $prefixNumbers = \core\Context::getInstance()->getPrefixNumbers();
        
        $l['payment_import_line_id'] = $this->getPaymentImportLineId();
        $l['import_status']          = $this->getImportStatus();
        $l['company_id']             = $this->getCompanyId();
        $l['person_id']              = $this->getPersonId();
        $l['customer_name']          = format_customername($this);
        $l['invoice_id']             = $this->getInvoiceId();
        $l['invoice_number']         = $this->getInvoiceId() ? $prefixNumbers . $this->getField('invoice_number') : '';
        $l['bankaccountno']          = $this->getBankaccountno();
        $l['bankaccountno_contra']   = $this->getBankaccountnoContra();
        $l['amount']                 = $this->getAmount();
        $l['name']                   = $this->getName();
        $l['description']            = $this->getDescription();
        
        return $l;
    }
    
}

