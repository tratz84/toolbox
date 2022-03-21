<?php


function map_active_vat_tarifs() {
    $invoiceService = object_container_get(invoice\service\InvoiceService::class);
    
    $vats = $invoiceService->readActiveVatTarifs();
    $map = array();
    foreach($vats as $v) {
        $map[$v->getVatId()] = $v->getDescription();
    }

    return $map;
}


