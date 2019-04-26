<?php


namespace webmail\service;


use core\service\ServiceBase;
use webmail\model\TemplateDAO;
use webmail\form\TemplateForm;
use webmail\model\Template;
use webmail\model\TemplateToDAO;

class EmailTemplateService extends ServiceBase {
    
    
    public function readByTemplateCode($c) {
        $tDao = new TemplateDAO();
        $t = $tDao->readByCode($c);
        if (!$t)
            return $t;
        
        return $this->readTemplate($t->getTemplateId());
    }
    
    
    public function readAllTemplates() {
        $tDao = new TemplateDAO();
        
        return $tDao->readAll();
    }
    
    public function readActiveTemplates() {
        $tDao = new TemplateDAO();
        
        return $tDao->readActive();
    }
    
    public function readTemplate($id) {
        $tDao = new TemplateDAO();
        $tpl = $tDao->read($id);
        
        if (!$tpl) {
            return null;
        }
        
        $ttDao = new TemplateToDAO();
        $tts = $ttDao->readByTemplate($id);
        $tpl->setTemplateTos($tts);
        
        return $tpl;
    }
    
    public function saveTemplate(TemplateForm $form) {
        $id = $form->getWidgetValue('template_id');
        if ($id) {
            $template = $this->readTemplate($id);
        } else {
            $template = new Template();
        }
        
        $form->fill($template, array('template_id', 'template_code', 'name', 'subject', 'content', 'active'));
        
        $r = $template->save();
        
        $form->getWidget('template_id')->setValue($template->getTemplateId());
        
        
        $ttDao = new TemplateToDAO();
        $arrTemplateTos = $form->getWidget('templateTos')->getObjects();
        $ttDao->mergeFormListMTO1('template_id', $template->getTemplateId(), $arrTemplateTos);
        
        return $r;
    }
    
    public function deleteTemplate($id) {
        
        $ttDao = new TemplateToDAO();
        $ttDao->deleteByTemplate($id);
        
        $tDao = new TemplateDAO();
        $tDao->delete($id);
    }
    
    public function updateTemplateSort($templateIds) {
        if (is_string($templateIds)) {
            $templateIds = explode(',', $templateIds);
        }
        
        $tDao = new TemplateDAO();
        $tDao->updateSort($templateIds);
        
    }
    
    
}