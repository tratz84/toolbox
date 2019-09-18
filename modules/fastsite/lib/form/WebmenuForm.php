<?php


namespace fastsite\form;

use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\SelectImageTextField;
use core\forms\TextField;
use fastsite\service\WebpageService;
use core\forms\SelectField;
use fastsite\service\WebmenuService;

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
        
        
        $this-> addValidator('parent_webmenu_id', function($form) {
            $webmenu_id = $form->getWidgetValue('webmenu_id');
            $parent_webmenu_id = $form->getWidgetValue('parent_webmenu_id');
            
            // nothing selected? => ok..
            if (!$parent_webmenu_id) {
                return;
            }
            
            if ($webmenu_id && $webmenu_id == $parent_webmenu_id) {
                return 'Parent gelijk aan huidig menu-item';
            }
            
            // TODO: check if parent_webmenu_id is submenu of current item
            
            
        });
        
        
    }
    
    
    protected function addParentWebmenu( ){
        $webmenuService = object_container_get(WebmenuService::class);
        
        $items = $webmenuService->readMenusByParent(null, true);
        
        $l = $this->fillWebmenuArray($items);
        
        $this->addWidget(new SelectField('parent_webmenu_id', '', $l, 'Parent'));
        
    }
    
    protected function fillWebmenuArray($webmenuItems, $spaces='') {
        $arr = array();
        
        if ($spaces == '') {
            $arr[''] = 'Maak uw keuze';
        }
        
        foreach($webmenuItems as $wi) {
            $arr[$wi->getWebmenuId()] = $spaces . ' ' . $wi->getSummary();
            
            $children = $wi->getChildren();
            if (count($children)) {
                $arr2 = $this->fillWebmenuArray($children, $spaces.'--');
                
                foreach($arr2 as $key => $val) {
                    $arr[$key] = $val;
                }
            }
        }
        return $arr;
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
        
        
        $this->addWidget(new SelectField('webpage_id', '', $sites, 'Webpage'));
    }
    
}

