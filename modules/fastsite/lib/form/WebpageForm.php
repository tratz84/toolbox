<?php

namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TinymceField;
use fastsite\data\FastsiteSettings;
use fastsite\service\WebpageService;

class WebpageForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new HiddenField('webpage_id'));
        
        $this->addWidget(new HtmlField('rev', '', 'Revision'));
        
        $this->addWidget(new CheckboxField('active', null, 'Active'));
        $this->addWidget(new TextField('code', '', 'Code'));
        $this->getWidget('code')->setInfoText('Code gebruikt wordt vanuit de programmeercode om aan deze pagina te refereren');
        
        $this->addTemplateSelection();
        $this->addWidget(new SelectField('module', '', array('' => 'Default'), 'Module'));
        $this->addWidget(new TextField('url', '', 'Url'));
        
        $this->addWidget(new TextField('meta_title',       '', 'Titel'));
        $this->addWidget(new TextField('meta_description', '', 'Meta description'));
        $this->addWidget(new TextField('meta_keywords',    '', 'Meta keywords'));
        
        $this->addWidget(new TinymceField('content1', '', 'Content 1'));
        $this->addWidget(new TinymceField('content2', '', 'Content 2'));
        $this->addWidget(new HtmlField('edited', '', 'Laatst bewerkt'));
        $this->addWidget(new HtmlField('created', '', 'Aangemaakt op'));
        
        
        
        $this->addValidator('code', function($form) {
            $code = trim( $form->getWidgetValue('code') );
            
            $webpageService = object_container_get(WebpageService::class);
            $webpage = $webpageService->readWebpageByCode($code);
            if (!$webpage) {
                return null;
            }
            
            if ($webpage->getWebpageId() != $form->getWidgetValue('webpage_id')) {
                return 'Code reeds in gebruik';
            }
        });
        
        $this->addValidator('url', function($form) {
            $url = trim( $form->getWidgetValue('url') );
            
            $webpageService = object_container_get(WebpageService::class);
            $webpage = $webpageService->readWebpageByUrl($url);
            if (!$webpage) {
                return null;
            }
            
            if ($webpage->getWebpageId() != $form->getWidgetValue('webpage_id')) {
                return 'Url reeds in gebruik';
            }
        });
        
    }
    
    
    protected function addTemplateSelection() {
        $fastsiteSettings = object_container_get( FastsiteSettings::class );
        $templateSettings = $fastsiteSettings->getActiveTemplateSettings();
        $templateFiles = $templateSettings->getTemplateFiles();
        
        $map = array();
        $map[''] = 'Default template';
        foreach($templateFiles as $filename => $settings) {
            $map[$filename] = $settings['description'];
        }
        
        $this->addWidget(new SelectField('fastsite_template_file', '', $map, 'Template'));
        
        
    }
    
    
    
}
