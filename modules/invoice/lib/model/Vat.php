<?php


namespace invoice\model;


class Vat extends base\VatBase {

    public function __construct() {
        parent::__construct();
        
        $this->setVisible(true);
    }

}

