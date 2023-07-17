<?php


namespace webmail\service;


use core\service\ServiceBase;
use webmail\model\WebmailAzureTokenDAO;
use core\exception\ObjectNotFoundException;
use webmail\model\WebmailAzureToken;
use webmail\form\AzureTokenForm;
use webmail\azure\Azure365Auth;

class CloudTokenService extends ServiceBase {
    
    
    public function getAzureRedirectUri() {
        if (is_debug()) {
            $u = 'https://portal.itxplain.nl/itxplain/?m=webmail&c=externalTokens/azure/ret';
        }
        else {
            $u = BASE_URL . appUrl('/?m=webmail&c=azure/ret');
        }
        
        return $u;
    }
    
    
    public function readAzureTokens() {
        $watDao = new WebmailAzureTokenDAO();
        return $watDao->readAll();
    }
    
    public function readAzureToken( $webmailAzureTokenId ) {
        $watDao = new WebmailAzureTokenDAO();
        $wat = $watDao->read( $webmailAzureTokenId );
        
        if (!$wat) {
            throw new ObjectNotFoundException( 'AzureToken object not found' );
        }
        
        return $wat;
    }
    
    public function saveAzureRequestData( $webmailAzureTokenId, $data ) {
        $watDao = new WebmailAzureTokenDAO();
        $watDao->updateField( $webmailAzureTokenId, 'request_data', json_encode( $data ) );
    }
    public function saveAzureResponseData( $webmailAzureTokenId, $data ) {
        $watDao = new WebmailAzureTokenDAO();
        $watDao->updateField( $webmailAzureTokenId, 'response_data', $data );
    }
    
    public function saveAzureToken( AzureTokenForm $form ) {
        
        $id = $form->getWidgetValue( 'webmail_azure_token_id' );
        
        if ($id) {
            $wat = $this->readAzureToken( $id );
        }
        else {
            $wat = new WebmailAzureToken();
        }
        
        $form->fill( $wat, array('description', 'azure_authorization_url', 'azure_token_url', 'azure_client_id', 'azure_client_secret') );
        
        $wat->save();
        
        return $wat;
    }
    
    public function getAzureAccessToken( $webmailAzureTokenId ) {
        $wat = $this->readAzureToken( $webmailAzureTokenId );
        
        $json = json_decode( $wat->getResponseData() );
        
        if ($json && isset($json->access_token)) {
            if ( $wat->getExpiresIn() > 0 && $wat->getRefreshTimestamp() ) {
                $t = new \DateTime( $wat->getRefreshTimestamp() );
                $t->add( new \DateInterval( 'PT' . $wat->getExpiresIn() . 'S' ) );
                
                if ( $t->format('Y-m-d H:i:s') > date('Y-m-d H:i:s') ) {
                    return $json->access_token;
                }
            }
        }
        
        if ($json && isset($json->refresh_token)) {
            $azureAuth = new Azure365Auth();
            $azureAuth->loadAzureTokenById( $wat->getWebmailAzureTokenId() );
            $r = $azureAuth->refreshToken();
            
            if ($r && isset($r->access_token))
                return $r->access_token;
        }
        return false;
    }
    
    
    public function deleteAzureToken( $webmailAzureTokenId ) {
        $wat = $this->readAzureToken($webmailAzureTokenId);
        
        $watDao = new WebmailAzureTokenDAO();
        $watDao->delete( $webmailAzureTokenId );
    }
    
    
    
}




