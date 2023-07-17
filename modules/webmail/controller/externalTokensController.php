<?php


use core\controller\BaseController;
use webmail\service\CloudTokenService;
use webmail\form\AzureTokenForm;

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
        
        $this->form = new AzureTokenForm();
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                
            }
        }
        
        
        $this->isNew = true;
        
        
        return $this->render();
    }
    
    
    
    
}

