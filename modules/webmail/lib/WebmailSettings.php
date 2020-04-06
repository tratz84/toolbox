<?php

namespace webmail;


use webmail\service\EmailTemplateService;

class WebmailSettings {
    
    public function __construct() {
        
    }
    
    public function getTemplateIdNewMail() {
        return ctx()->getSetting('webmail_template_id_new_mail');
    }
    public function getTemplateIdReplyMail() {
        return ctx()->getSetting('webmail_template_id_reply_mail');
    }
    public function getTemplateIdForwardMail() {
        return ctx()->getSetting('webmail_template_id_forward_mail');
    }
    
    
    public function getTemplateNewMail() {
        $id = $this->getTemplateIdNewMail();
        
        $etService = object_container_get(EmailTemplateService::class);
        $tpl = $etService->readTemplate( $id );
        
        return $tpl;
    }
    
    public function getTemplateContentReplyMail() {
        $id = $this->getTemplateIdReplyMail();
        return $this->getTemplateContent($id);
    }
    
    public function getTemplateContentForwardMail() {
        $id = $this->getTemplateIdForwardMail();
        return $this->getTemplateContent($id);
    }
    
    
    public function getTemplateContent($templateId) {
        if (intval($templateId) == 0) {
            return '';
        }
        
        $etService = object_container_get(EmailTemplateService::class);
        $tpl = $etService->readTemplate( $templateId );
        
        if (!$tpl) {
            return '';
        }
        
        $dom = new \DOMDocument();
        @$dom->loadHTML( $tpl->getContent() );
        $elBody = @$dom->getElementsByTagName('body');
        if (count($elBody) == 0) {
            return $tpl->getContent();
        }
        
        $html = @$dom->saveHTML( $elBody[0] );
        
        $html = preg_replace('/<body.*?>/', '', $html);
        $html = str_replace('</body>', '', $html);
        
        return $html;
    }
    
    
}
