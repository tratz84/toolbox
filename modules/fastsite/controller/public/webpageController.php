<?php


use fastsite\FastsiteController;
use fastsite\service\WebpageService;

class webpageController extends FastsiteController {
    
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    
    public function action_index() {
        $webpageService = $this->oc->get(WebpageService::class);
        $this->webpage = $webpageService->readByUrl($_SERVER['REQUEST_URI']);
        
        if (!$this->webpage) {
            return $this->render404();
        }
        
        $this->title            = $this->webpage->getRevision()->getMetaTitle();
        $this->meta_description = $this->webpage->getRevision()->getMetaDescription();
        $this->meta_keywords    = $this->webpage->getRevision()->getMetaKeywords();
        $this->content          = $this->webpage->getRevision()->getContent1();
        $this->content1         = $this->webpage->getRevision()->getContent1();
        $this->content2         = $this->webpage->getRevision()->getContent2();
        
        $this->render();
    }
    
    
}
