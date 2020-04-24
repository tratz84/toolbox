<?php


use filesync\service\StoreService;

function mapArchiveStores() {
    $storeService = object_container_get(StoreService::class);
    
    $mapStores = array();
    $archiveStores = $storeService->readArchiveStores();
    foreach($archiveStores as $as) {
        $mapStores[ $as->getStoreId() ] = $as->getStoreName();
    }
    
    return $mapStores;
}


