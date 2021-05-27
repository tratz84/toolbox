<?php

namespace core\template;


class HtmlScriptLoader {
    
    protected $javascript = array();
    protected $css = array();
    protected $inlineCss = '';
    
    protected $enabledGroups = array();
    
    public function __construct() {
        
    }
    
    public function enableGroup($groupName) {
        if (in_array($groupName, $this->enabledGroups) == false) {
            $this->enabledGroups[] = $groupName;
        }
    }
    
    public function isGroupEnabled($groupName) {
        return in_array($groupName, $this->enabledGroups);
    }
    
    public function addInlineCss($css) { $this->inlineCss .= $css; }
    public function getInlineCss() { return $this->inlineCss; }
    
    public function registerJavascript($groupName, $url, $opts=array()) {
        if (isset($this->javascript[$groupName]) == false) {
            $this->javascript[$groupName] = array();
        }
        
        if (isset($opts['enabled']) && $opts['enabled'])
            $this->enableGroup($groupName);
        
        // default options
        if (isset($opts['position']) == false)
            $opts['position'] = 'top';
        
        $this->javascript[$groupName][] = array(
            'url' => $url,
            'opts' => $opts
        );
    }

    public function registerCss($groupName, $url, $opts=array()) {
        if (isset($this->css[$groupName]) == false) {
            $this->css[$groupName] = array();
        }
        
        if (isset($opts['enabled']) && $opts['enabled'])
            $this->enableGroup($groupName);
        
        // default options
        if (isset($opts['position']) == false)
            $opts['position'] = 'top';
        
        $this->css[$groupName][] = array(
            'url' => $url,
            'opts' => $opts
        );
    }
    
    protected function rewriteUrl($url) {
        if (strpos($url, 'https://') === 0 || strpos($url, 'https://') === 0) {
            return $url;
        } else {
            if (strpos($url, appUrl('/')) === 0) {
                return $url;
            }
            
            if (strpos($url, '/') === 0)
                $url = substr($url, 1);
            
            return BASE_HREF . $url;
        }
    }
    
    public function printCss($position) {
        foreach($this->css as $groupName => $data) {
            if ($this->isGroupEnabled($groupName) == false)
                continue;
            
            foreach($data as $css) {
                if ($css['opts']['position'] != $position)
                    continue;
                
                    print '<link href="'.esc_attr($this->rewriteUrl($css['url'])).'" rel="stylesheet" type="text/css" />' . "\n";
            }
        }
    }
    
    public function printJavascript($position) {
        foreach($this->javascript as $groupName => $data) {
            if ($this->isGroupEnabled($groupName) == false)
                continue;
            
            foreach($data as $js) {
                if ($js['opts']['position'] != $position)
                    continue;
                
                print '<script src="'.esc_attr($this->rewriteUrl($js['url'])).'"></script>' . "\n";
            }
        }
    }
}

