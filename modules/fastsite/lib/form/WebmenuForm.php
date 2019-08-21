<?php


namespace fastsite\form;

use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\SelectImageTextField;
use core\forms\TextField;
use fastsite\service\WebpageService;
use core\forms\SelectField;

class WebmenuForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addWidget(new HiddenField('webmenu_id'));
        $this->addWidget(new TextField('code', '', 'Code'));
        $this->addParentWebmenu();
        $this->addWidget(new TextField('label', '', 'Label'));
        $this->addWidget(new TextField('url', '', 'Url'));
        $this->addWebpageSelector();
        $this->addWidget(new TextField('description', '', 'Omschrijving'));
        
    }
    
    
    protected function addWebpageSelector() {
        $webpageService = object_container_get(WebpageService::class);
        
        $webpages = $webpageService->readAllWebpages();
        if (count($webpages) == 0) {
            return;
        }
        
        $sites = array();
        $sites[''] = 'Maak uw keuze';
        
        foreach($webpages as $w) {
            $t = '';
            
            if ($w->getCode()) {
                $t = $t . $w->getCode() . ' - ';
            }
            
            if ($w->getUrl()) {
                $t = $t . $w->getUrl();
            } else {
                $t = $t . ' ' . $w->getWebpageId();
            }
            
            $sites[$w->getWebpageId()] = $t; 
        }
        
        
        $this->addWidget(new SelectField('webpage_page_id', '', $sites, 'Webpage'));
    }
    
}

