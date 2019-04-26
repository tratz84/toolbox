<?php


namespace webmail\model;


class Template extends base\TemplateBase {

    protected $templateTos;
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive(true);
    }
    
    
    public function getTemplateTos() { return $this->templateTos; }
    public function setTemplateTos($t) { $this->templateTos = $t; }

    
    public function render($params = array()) {
        return apply_html_vars($this->getContent(), $params);
    }
    
}

