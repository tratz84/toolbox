<?php

use core\controller\BaseController;
use base\service\MetaService;
use filesync\form\FilesyncArchiveSettingsForm;
use filesync\form\ArchiveFileUploadForm;
use filesync\service\StoreService;

class archiveWidgetController extends BaseController {
    
    public function action_index() {
        // 
        $userId = $this->ctx->getUser()->getUserId();
        
        // widget settings
        $metaService = object_container_get(MetaService::class);
        $widgetSettings = @unserialize( $metaService->getMetaValue('user', $userId, 'dashboard_filesyncArchive-widget') );
        
        
        $this->form = new ArchiveFileUploadForm(['store_as_list' => true]);
        $this->form->setAction(appUrl('/?m=filesync&c=archive&a=upload'));
        
        if ($widgetSettings && isset($widgetSettings['store_id']))
            $this->form->getWidget('store_id')->setValue($widgetSettings['store_id']);
        $this->form->getWidget('document_date')->setValue(date('d-m-Y'));

        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    
    public function action_settings() {
        $userId = $this->ctx->getUser()->getUserId();
        
        $metaService = object_container_get(MetaService::class);
        $widgetSettings = @unserialize( $metaService->getMetaValue('user', $userId, 'dashboard_filesyncArchive-widget') );

        $this->form = new FilesyncArchiveSettingsForm();
        if (is_array($widgetSettings)) {
            $this->form->bind( $widgetSettings );
        }
        
        if (get_var('filesync_archive_settings')) {
            $this->form->bind($_REQUEST);
            
            $widgetSettings = $this->form->asArray();
            $metaService->saveMeta('user', $userId, 'dashboard_filesyncArchive-widget', serialize($widgetSettings));
            
            return $this->json(array(
                'success' => true
            ));
        }
        
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
}