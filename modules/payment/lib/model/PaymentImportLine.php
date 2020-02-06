<?php


namespace payment\model;


class PaymentImportLine extends base\PaymentImportLineBase {


    public function generateTransactionId() {
        
        $str = '';
        $str .= $this->getPaymentDate();
        $str .= $this->getDescription();
        $str .= $this->getBankaccountnoContra();
        $str .= $this->getAmount();
        
        $trxid = crc32( $str );
        
        $this->setTransactionId($trxid);
    }
}

