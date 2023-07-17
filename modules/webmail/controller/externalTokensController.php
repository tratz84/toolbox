<?php


use core\controller\BaseController;
use webmail\azure\Azure365Auth;
use webmail\form\AzureTokenForm;
use webmail\service\CloudTokenService;

class externalTokensController extends BaseController {
    
    
    public function action_index() {
        
        $ctService = object_container_get( CloudTokenService::class );
        
        $this->azureTokens = $ctService->readAzureTokens();
        
        
        
        return $this->render();
    }
    
    
    public function action_add() {
        return $this->render();
    }
    
    
    public function action_edit_azure() {
        
        $ctService = object_container_get( CloudTokenService::class );
        
        $this->form = new AzureTokenForm();
        
        $wat = null;
        
        if (get_var('id')) {
            $wat = $ctService->readAzureToken( get_var('id') );
            $this->form->bind( $wat );
            $this->isNew = false;
        }
        else {
            $this->isNew = true;
        }
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                $wat = $ctService->saveAzureToken( $this->form );
                
                if (get_var('request_token')) {
                    // redirect to auth request
                    $a = new Azure365Auth( );
                    $a->loadAzureTokenById( $wat->getWebmailAzureTokenId() );
                    
                    if ($a->getEndpoint() != '') {
//                         print $a->getRedirectAuthUrl();exit;
                        header( 'Location: ' . $a->getRedirectAuthUrl() );
                        exit;
                    }
                    else {
                        report_user_warning( t('Authorization url not set') );
                    }
                }
                
                redirect('/?m=webmail&c=externalTokens&a=edit_azure&id='.$wat->getWebmailAzureTokenId());
            }
        }
        
        if ($wat) {
            $r = $wat->getResponseData();
            
            if ($r) {
                $json = json_decode( $r );
                
                if ($json && isset($json->token_type)) {
                    $this->form->getWidget('status')->setValue( 'OK' );
                }
                else if ($json) {
                    if (isset($json->error)) {
                        $this->form->getWidget('status')->setValue( t('Error') . ': '. $json->error . ', '. $json->error_description );
                    }
                    else {
                        $this->form->getWidget('status')->setValue( t('Unknown error') );
                    }
                }
                else {
                    $this->form->getWidget('status')->setValue( t('Invalid response') );
                }
            }
            else {
                $this->form->getWidget('status')->setValue( t('Token not set') );
            }
        }
        
        $this->form->getWidget('request_token')->setValue(0);
        
        return $this->render();
    }
    
    
    public function action_delete() {
        $ctService = object_container_get( CloudTokenService::class );
        $ctService->deleteAzureToken( get_var('id') );
        
        
        redirect( '/?m=webmail&c=externalTokens' );
    }
    
    
    
    
}

