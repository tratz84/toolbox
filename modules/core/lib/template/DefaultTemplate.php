<?php
namespace core\template;

class DefaultTemplate
{

    var $mTpl;

    var $mVars;

    var $mSubTpls = array();
    
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

    function showTemplate()
    {
        foreach ($this->mVars as $k => $v) {
            $$k = $v;
        }

        include $this->mTpl;
    }

    function getTemplate()
    {
        foreach ($this->mVars as $k => &$v) {
            $$k = $v;
        }

        ob_start();
        include $this->mTpl;
        $buf = ob_get_clean();
        
        $buf .= $this->footerHtml;

        return $buf;
    }
}