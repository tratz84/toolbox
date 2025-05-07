<?php


namespace webmail\model;


use webmail\service\EmailTemplateService;

class Template extends base\TemplateBase {

    protected $templateTos;
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive(true);
    }
    
    
    public function getTemplateTos() { return $this->templateTos; }
    public function setTemplateTos($t) { $this->templateTos = $t; }

    
    public function render($params = array()) {
        $html = apply_html_vars($this->getContent(), $params);
        
        return apply_filter('webmail-template-render', $html);
    }
    
    
    public function masterRender($params = array()) {
        $html = apply_html_vars($this->getContent(), $params);
        $html = apply_filter('webmail-template-render', $html);
        
        
        if ($this->getMasterTemplateId()) {
            $etservice = object_container_get( EmailTemplateService::class );
            $mt = $etservice->readMasterTemplate( $this->getMasterTemplateId() );
            
            $masterhtml = $mt->getContent();
            if (strpos($masterhtml, '[[content]]') !== false) {
                $html = str_replace('[[content]]', $html, $masterhtml);
            }
        }
        
        return $html;
    }
    
}

