<?php

namespace core;

use base\service\SettingsService;
use core\exception\InvalidStateException;

class Context
{

    protected $vars = array();
    
    protected $moduleDirs = array();

    protected $customer;
    
    protected $contextName = null;                      // peopleweb__customer.contextName
    protected $user = null;
    protected $selectedLang = 'nl_NL';
    
    protected $module = 'base';
    protected $controller = 'dashboard';

    protected $action = 'index';
    
    protected $enabledModules = array( 'core' );
    
    
    protected $dateFormat = 'd-m-Y';
    protected $datetimeFormat = 'd-m-Y H:i:s';
    
    protected $settings = null;
    
    public function __construct() {
        
    }
    
    
    public function addModuleDir($dir) {
        $this->moduleDirs[] = $dir;
        module_list(true);
    }
    public function getModuleDirs() { return $this->moduleDirs; }
    
    public function getDateFormat() { return $this->dateFormat; }
    public function getDatetimeFormat() { return $this->datetimeFormat; }
    
    // customer from pw_master.peopleweb__customer-database
    public function setCustomer($customer) { $this->customer = $customer; }
    public function getCustomer() { return $this->customer; }
    
    public function setContextName($n) {
        if ($this->contextName != null)
            throw new InvalidStateException('contextName already set');
        
        $this->contextName = $n;
    }
    public function getContextName() { return $this->contextName; }
    
    // experimental features enabled?
    public function isExperimental() {
        return $this->customer->getExperimental();
    }
    
    public function setUser($u) { $this->user = $u; }
    public function getUser() { return $this->user; }
    
    
    public function getSelectedLang() { return $this->selectedLang; }
    public function setSelectedLang($p) { $this->selectedLang = $p; }

    public function isModuleEnabled($name) {
        return in_array($name, $this->enabledModules);
    }
    
    public function setEnabledModules($modules) {
        $this->enabledModules = array();
        
        foreach($modules as $m) {
            $this->enableModule($m);
        }
    }
    
    public function enableModule($name) {
        $modules = module_list();
        
        // check if module exists?
        if (isset($modules[$name]) == false)
            throw new InvalidStateException('Module not found ('.$name.')');
        
        if (in_array($name, $this->enabledModules) == false) {
            $this->enabledModules[] = $name;
        }
    }
    
    public function getEnabledModules() { return $this->enabledModules; }
    
    
    public function getModule() { return $this->module; }
    public function setModule($m) {
        $m = preg_replace('/[^a-zA-Z0-9_\\-\\/]/', '', $m);
        $this->module = $m;
    }

    public function setController($controller) {
        $controller = preg_replace('/[^a-zA-Z0-9_\\-\\/]/', '', $controller);
        $this->controller = $controller;
    }

    public function getController() {
        return $this->controller;
    }

    public function setAction($action) {
        $action = preg_replace('/[^a-zA-Z0-9_\\-\\/]/', '', $action);
        $this->action = $action;
    }

    public function getAction() {
        return $this->action;
    }

    public function getDataDir() {
        if ($this->getContextName() == null)
            throw new InvalidStateException('Context not set');
        
        $path = DATA_DIR . '/' . $this->getContextName();
        if (is_dir($path) == false)
            mkdir($path);
        
        $path = realpath($path);
        
        if ($path == false)
            throw new InvalidStateException('DATA_DIR not configured');
        
        return $path;
    }
    

    function setVar($key, $value)
    {
        $this->vars[$key] = $value;
    }

    function getVar($key, $defaultVal = null) {
        if (isset($this->vars[$key]))
            return $this->vars[$key];
        else if (isset($_POST[$key]))
            return $_POST[$key];
        else if (isset($_GET[$key]))
            return $_GET[$key];
        else
            return $defaultVal;
    }
    
    public function flushSettingCache() {
        $this->settings = null;
    }
    
    function getSetting($val, $defaultVal=null) {
        if ($this->settings == null) {
            $ss = ObjectContainer::getInstance()->get(SettingsService::class);
            $this->settings = $ss->settingsAsMap();
        }
        
        if (isset($this->settings[$val])) {
            return $this->settings[$val];
        } else {
            return $defaultVal;
        }
    }
    
    public function getPageSize() { return $this->getSetting('PAGE_SIZE'); }
    public function isPersonsEnabled() { return $this->getSetting('personsEnabled') ? true : false; }
    public function isCompaniesEnabled() { return $this->getSetting('companiesEnabled') ? true : false; }
    public function getLogoFile() { return $this->getSetting('logoFile'); }
    
    public function isInvoiceModuleEnabled() { return $this->getSetting('invoiceModuleEnabled', false); }
    
    public function getCompanyName()      { return trim($this->getSetting('companyName')); }
    public function getCompanyStreet()    { return trim($this->getSetting('companyStreet')); }
    public function getCompanyZipcode()   { return trim($this->getSetting('companyZipcode')); }
    public function getCompanyCity()      { return trim($this->getSetting('companyCity')); }
    public function getCompanyPhone()     { return trim($this->getSetting('companyPhone')); }
    public function getCompanyEmail()     { return trim($this->getSetting('companyEmail')); }
    public function getCompanyCocNumber() { return trim($this->getSetting('companyCocNumber')); }
    public function getCompanyIBAN()      { return trim($this->getSetting('companyIBAN')); }
    public function getCompanyVat()       { return trim($this->getSetting('companyVat')); }
    
    public function getPrefixNumbers()    { return trim($this->getSetting('prefixNumbers')); }
    
    

    public static function &getInstance() {
        static $instance;

        if (! $instance) {
            $instance = new Context();
        }

        return $instance;
    }
}


