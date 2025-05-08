<?php


namespace webmail\service;


use core\service\ServiceBase;
use webmail\model\TemplateDAO;
use webmail\form\TemplateForm;
use webmail\model\Template;
use webmail\model\TemplateToDAO;
use core\exception\DatabaseException;
use webmail\WebmailSettings;
use webmail\model\MasterTemplateDAO;
use webmail\model\MasterTemplate;
use core\exception\ObjectNotFoundException;
use base\util\ActivityUtil;

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
        
        $form->fill($template, array('template_id', 'master_template_id', 'template_code', 'name', 'subject', 'content', 'active'));
        
        $r = $template->save();
        
        if (!$r) {
            throw new DatabaseException('Error saving Template');
        }
        
        $form->getWidget('template_id')->setValue($template->getTemplateId());
        
        
        $ttDao = new TemplateToDAO();
        $arrTemplateTos = $form->getWidget('templateTos')->getObjects();
        $ttDao->mergeFormListMTO1('template_id', $template->getTemplateId(), $arrTemplateTos);
        
        return $template;
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
    
    
    
    public function saveMasterTemplate( $form ) {
        $id = (int)$form->getWidgetValue('master_template_id');
        if ($id) {
            $mt = $this->readMasterTemplate($id);
        }
        else {
            $mt = new MasterTemplate();
        }
        
        $isnew = $mt->isNew();
        
        $form->fill( $mt, array('name') );
        
        $html = $form->getField('content');
        if ($html) {
            $mt->setFilename( $form->getField('filename') );
            $mt->setContent( $html );
        }
        
        if ($isnew) {
            ActivityUtil::logActivityRefObject(MasterTemplate::class, $mt->getMasterTemplateId(), 'created', 'Master templated created');
        }
        else {
            ActivityUtil::logActivityRefObject(MasterTemplate::class, $mt->getMasterTemplateId(), 'changed', 'Master templated changed');
        }
        
        $mt->save();
        
        return $mt;
    }
    
    
    
    public function listMasterTemplates() {
        $mtdao = new MasterTemplateDAO();
        
        return $mtdao->readAll();
    }
    
    public function readMasterTemplate( $id ) {
        $mtdao = new MasterTemplateDAO();
        $mt = $mtdao->read( $id );
        
        if (!$mt) {
            throw new ObjectNotFoundException( 'MasterTemplate not found' );
        }
        
        
        return $mt;
    }
    
    
    public function deleteMasterTemplate( $id ) {
        
        $mt = $this->readMasterTemplate($id);
        $mt->delete();
        
        $tdao = new TemplateDAO();
        $tdao->unsetMasterTemplateId( $mt->getMasterTemplateId() );
        
        
        ActivityUtil::logActivityRefObject(MasterTemplate::class, $mt->getMasterTemplateId(), 'deleted', 'Master templated deleted');
    }
    
    
}


