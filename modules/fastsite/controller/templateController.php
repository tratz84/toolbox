<?php


use core\controller\BaseController;
use fastsite\WebsiteTemplateService;
use fastsite\form\WebsiteTemplateForm;

class templateController extends BaseController {
    
    
    public function action_index() {
        
        $wtService = $this->oc->get( WebsiteTemplateService::class );

        $this->templates = $wtService->getTemplates();
        
        
        return $this->render();
    }
    
    
    public function action_add() {
        
        $this->form = new WebsiteTemplateForm();
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
        }
        
        
        return $this->render();
    }
    
    
}
