<?php


namespace invoice\model;


class InvoiceLine extends base\InvoiceLineBase {

    /*
     * NOTE about vat_amount field, this field has 2 purposes
     *      - rounding vat for migrating other system to Toolbox
     *      - support for articles costing €10,- incl. vat, to support both article prices of "8.26 + 1.74-vat" and "8.27 + 1.73-vat" <= this has to be implemented..
     */

    public function getTotalPriceExclVat() {
        return myround(($this->getAmount() * $this->getPrice()), 2);
    }
    
    public function getTotalPriceInclVat() {
        $p = $this->getTotalPriceExclVat();
        $v = myround($p * $this->getVatPercentage() / 100, 2);
        return myround($p + $v, 2);
    }
    
    public function calculateVatAmount() {
        $vatAmount = myround( $this->getPrice() * strtodouble($this->getAmount() * $this->getVatPercentage() / 100), 2);
        
        $this->setVatAmount( $vatAmount );
    }
    
}

