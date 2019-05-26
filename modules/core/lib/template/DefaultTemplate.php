<?php
namespace core\template;

class DefaultTemplate
{

    var $mTpl;

    var $mVars;

    var $mSubTpls = array();
    
    protected $html = null;
    
    protected $footerHtml = '';

    function __construct($templatePath)
    {
        $p = realpath($templatePath);

        if (! file_exists($p)) // check if file exists & template is in template directory
            throw new \Exception('Template not found, ' . $p);

        $this->mTpl = $p;
        $this->mVars = array();
    }
    
    public function addFooterHtml($html) {
        $this->footerHtml .= $html;
    }

    // main template
    function setVar($aName, $aValue, $aSafeVar = false)
    {
        if ($aSafeVar)
            $this->mVars[$aName] = htmlescape($aValue);
        else
            $this->mVars[$aName] = $aValue;
    }

    function getVar($aName)
    {
        return $this->mVars[$aName];
    }
    
    public function getTemplateFile() { return $this->mTpl; }
    
    public function getHtml() { return $this->html; }
    public function setHtml($h) { $this->html = $h; }

    function showTemplate($opts=array())
    {
        foreach ($this->mVars as $k => $v) {
            $$k = $v;
        }

        ob_start();
        include $this->mTpl;
        
        if ($this->footerHtml) {
            print $this->footerHtml;
        }
        $buf = ob_get_clean();
        
        $this->setHtml( $buf );
        
        hook_eventbus_publish($this, 'core', 'template-showTemplate');
        
        if (isset($opts['return']) && $opts['return']) {
            return $this->getHtml();
        } else {
            print $this->getHtml();
        }
    }

    function getTemplate()
    {
        return $this->showTemplate(['return' => true]);
    }
}

