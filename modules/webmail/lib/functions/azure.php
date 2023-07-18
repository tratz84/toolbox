<?php



function map_azureOptions() {
    $ctService = object_container_get( \webmail\service\CloudTokenService::class );
    $azureTokens = $ctService->readAzureTokens();
    
    $mapTokens = array();
    $mapTokens[''] = t('Make your choice');
    foreach($azureTokens as $at) {
        $mapTokens[ $at->getWebmailAzureTokenId() ] = $at->getDescription();
    }
    
    return $mapTokens;
}

