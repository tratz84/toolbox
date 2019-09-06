<?php




function fastsite_webpage_by_id($id) {
    $webpageService = object_container_get(\fastsite\service\WebpageService::class);
    
    $w = $webpageService->readWebpage( $id );
    
    return $w;
}

function fastsite_webpage_by_code($code) {
    $webpageService = object_container_get(\fastsite\service\WebpageService::class);
    
    $w = $webpageService->readWebpageByCode( $code );
    
    return $w;
}

