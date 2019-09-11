<?php
/**
 * FastsiteSettings - configuration data of fastsite-module
 */

namespace fastsite\data;


use core\exception\InvalidStateException;

class FastsiteSettings extends FileDataBase {
    
    protected static $instance = null;
    
    
    public function __construct() {
        
    }
    
    public function setActiveTemplate($n) { $this->setValue('active_template', $n); }
    public function getActiveTemplate() { return $this->getValue('active_template', null); }
    
    public function getActiveTemplateSettings() {
        $n = $this->getActiveTemplate();
        
        if (!$n) {
            throw new InvalidStateException('No template active');
        }
        
        $ts = new FastsiteTemplateSettings($n);
        if (!$ts->load()) {
            throw new InvalidStateException('Settings not found for active template');
        }
        
        return $ts;
    }
    
    
    public function save($f=null) {
        return parent::save('fastsite-settings.php');
    }
    
    public function load($f=null) {
        return parent::load('fastsite-settings.php');
    }
    
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new FastsiteSettings();
            self::$instance->load();
        }
        
        return self::$instance;
    }
}
