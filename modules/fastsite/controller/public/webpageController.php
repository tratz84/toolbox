<?php


use fastsite\FastsiteController;
use fastsite\service\WebpageService;

class webpageController extends FastsiteController {
    
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    
    public function action_index() {
        $webpageService = $this->oc->get(WebpageService::class);
        $webpage = $webpageService->readByUrl($_SERVER['REQUEST_URI']);
        
        if (!$webpage) {
            return $this->render404();
        }
        
        
        $this->render();
    }
    
    
}
