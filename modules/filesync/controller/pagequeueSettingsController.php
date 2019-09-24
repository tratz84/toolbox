<?php


use core\controller\BaseController;
use filesync\form\PagequeueSettingsForm;
use core\Context;
use base\service\SettingsService;

class pagequeueSettingsController extends BaseController {
    
    public function action_index() {
        
        $this->form = new PagequeueSettingsForm();
        
        $ctx = Context::getInstance();
        $this->form->getWidget('default_rotation')->setValue($ctx->getSetting('filesync__pagequeue_default_rotation'));
        $this->form->getWidget('archive_store')->setValue($ctx->getSetting('filesync__pagequeue_archive_store'));
//         $this->form->setWidgetValue('archive_store', $ctx->getSetting('filesync__pagequeue_archive_store'));
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            $settingsService = object_container_get(SettingsService::class);
            $settingsService->updateValue('filesync__pagequeue_default_rotation', $this->form->getWidgetValue('default_rotation'));
            $settingsService->updateValue('filesync__pagequeue_archive_store', $this->form->getWidgetValue('archive_store'));
            
            redirect('/?m=base&c=masterdata/index');
        }
        
        
        return $this->render();
    }
}