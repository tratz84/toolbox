<?php


use core\controller\BaseController;
use core\exception\InvalidStateException;
use webmail\service\CloudTokenService;
use webmail\azure\Azure365Auth;

class retController extends BaseController {
    
    
    public function action_index() {
        
        $state = get_var('state');
        if (!$state || preg_match('/^wattok\\d+$/', $state) == false) {
            throw new InvalidStateException( 'No or invalid state-var set' );
        }
        
        $watId = (int)substr( $state, strlen('wattok') );
        
        $ctService = object_container_get( CloudTokenService::class );
        $wat = $ctService->readAzureToken( $watId );
        
        $requestData = json_decode( $wat->getRequestData() );
        
        if (get_var('error')) {
            $ctService->saveAzureResponseData( $wat->getWebmailAzureTokenId(), json_encode($_GET) );
        }
        else {
            $oa = new Azure365Auth();
            $oa->loadAzureTokenById( $wat->getWebmailAzureTokenId() );
            $oa->setEndpoint( $wat->getAzureTokenUrl() );
            $oa->setCode( get_var('code') );
            $oa->setCodeVerifier( $requestData->code_verifier );
            
            $r = $oa->requestToken();
        }
        
//         print $r;ex
        
        redirect( '/?m=webmail&c=externalTokens&a=edit_azure&id='.$wat->getWebmailAzureTokenId() );
    }
    
    
}