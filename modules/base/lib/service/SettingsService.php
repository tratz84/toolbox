<?php

namespace base\service;

use core\service\ServiceBase;
use base\model\SettingDAO;
use base\model\Setting;
use core\module\ModuleMeta;
use core\Context;

class SettingsService extends ServiceBase {
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    
    
    public function settingsAsMap() {
        $sDao = new SettingDAO();
        $list = $sDao->readKeyValue();
        
        $map = array();
        foreach($list as $s) {
            $map[$s->getSettingCode()] = $s->getTextValue();
        }

        
        if (isset($map['PAGE_SIZE']) == false)
            $map['PAGE_SIZE'] = 15;
            
        if (isset($map['personsEnabled']) == false)
            $map['personsEnabled'] = 0;
        
        if (isset($map['companiesEnabled']) == false)
            $map['companiesEnabled'] = 0;
                
        if (isset($map['invoiceModuleEnabled']) == false) {
            $map['offerModuleEnabled'] = 0;
            $map['invoiceModuleEnabled'] = 0;
        }
                
        if (isset($map['rentalModuleEnabled']) == false)
            $map['rentalModuleEnabled'] = 0;
                
        if (isset($map['calendarModuleEnabled']) == false)
            $map['calendarModuleEnabled'] = 0;
        
        if (isset($map['webmailModuleEnabled']) == false)
            $map['webmailModuleEnabled'] = 0;
                
        if (isset($map['reportModuleEnabled']) == false)
            $map['reportModuleEnabled'] = 0;
        
        if (isset($map['signrequestModuleEnabled']) == false)
            $map['signrequestModuleEnabled'] = 0;
        
        if (isset($map['invoice__orderType']) == false)
            $map['invoice__orderType'] = 'invoice';
        
        if (isset($map['master_base_color']) == false) {
            $map['master_base_color'] = '#ff0000';
        }

        if (isset($map['progressive_web_app_features']) == false)
            $map['progressive_web_app_features'] = 1;
        
        return $map;
    }
    
    public function updateValue($settingCode, $val, $opts=array()) {
        $sDao = new SettingDAO();
        $s = $sDao->readByKey($settingCode);
        
        if (!$s) {
            $s = new Setting();
            $s->setSettingCode($settingCode);
        }
        
        if (isset($opts['type'])) {
            $s->setSettingType( $opts['type'] );
        }
        
        $s->setTextValue($val);
        
        $r = $s->save();
        
        
        // flush settings
        if ($r) {
            ctx()->flushSettingCache();
        }
        
        return $r;
    }
    
    
    public function deleteSetting($settingCode) {
        $sDao = new SettingDAO();
        $s = $sDao->readByKey($settingCode);
        
        if ($s) {
            $s->delete();
        }
    }
    
    
    /**
     * enabledModulesChanged() - returns unique int-signature of current enabled modules
     * 
     */
    public function enabledModulesSignature() {
        $sDao = object_container_get( SettingDAO::class );
        
        $objs = $sDao->readByType('mod-activation');
        
        $l = array();
        foreach($objs as $obj) {
            $l[] = $obj->getFields();
        }
        
        return crc32( serialize($l) );
    }
    
    
    public function getModuleList() {
        $ctx = Context::getInstance();
        
        $objMetas = array();
        
        
        $modules = module_list();
        foreach($modules as $moduleName => $path) {
            $meta_file = $path . '/meta.php';
            if (file_exists($meta_file)) {
                $metas = require $meta_file;
                
                if (is_array($metas) == false) {
                    $metas = array($metas);
                }
                
                foreach($metas as $meta) {
                    if (is_a($meta, ModuleMeta::class)) {
                        $objMetas[] = $meta;
                    }
                }
            }
        }
        
        return $objMetas;
    }
    
}


