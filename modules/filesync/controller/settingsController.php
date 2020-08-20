<?php


use core\controller\BaseController;
use filesync\form\PagequeueSettingsForm;
use core\Context;
use base\service\SettingsService;
use filesync\FilesyncSettings;

class settingsController extends BaseController {
    
    public function init() {
        checkCapability('filesync', 'manager');
    }
    
    
    public function action_index() {
        
        $filesyncSettings = object_container_get( FilesyncSettings::class );
        
        $this->form = new PagequeueSettingsForm();
        
        $this->form->getWidget('libreoffice_previews')->setValue( $filesyncSettings->getLibreOfficePreviews() );
        
        $this->form->getWidget('default_rotation')->setValue( $filesyncSettings->getPagequeueDefaultRotation() );
        $this->form->getWidget('archive_store')->setValue( $filesyncSettings->getPagequeueArchiveStore() );
//         $this->form->setWidgetValue('archive_store', $ctx->getSetting('filesync__pagequeue_archive_store'));
        
        $this->form->getWidget('wopi_active')->setValue( $filesyncSettings->getWopiActive() );
        $this->form->getWidget('wopi_access_token_ttl')->setValue( $filesyncSettings->getWopiAccessTokenTtl() );
        
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            $settingsService = object_container_get(SettingsService::class);
            $settingsService->updateValue('filesync__libreoffice_previews', $this->form->getWidgetValue('libreoffice_previews')?1:0);
            $settingsService->updateValue('filesync__pagequeue_default_rotation', $this->form->getWidgetValue('default_rotation'));
            $settingsService->updateValue('filesync__pagequeue_archive_store', $this->form->getWidgetValue('archive_store'));
            
            $settingsService->updateValue('filesync__wopi_active', $this->form->getWidgetValue('wopi_active'));
            $settingsService->updateValue('filesync__wopi_access_token_ttl', $this->form->getWidgetValue('wopi_access_token_ttl'));
            
            report_user_message(t('Changes saved'));
            redirect('/?m=filesync&c=settings');
        }
        
        
        return $this->render();
    }
}