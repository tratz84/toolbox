<?php

namespace core\db;

use base\service\SettingsService;
use core\Context;
use core\exception\InvalidStateException;
use core\exception\MonitoringAlertException;

class DatabaseUpdater {
    
    protected $settingsKey = null;
    protected $version = 0;
    protected $updateFolder;
    
    /**
     * 
     * @param string $settingsKey  - value of base__setting.setting_code, ie 'CALENDAR_MODULE_VERSION'
     * @param int    $version      - current version number
     * @param string $updateFolder - folder containing sql update files
     */
    public function __construct($settingsKey, $version, $updateFolder) {
        $this->settingsKey = $settingsKey;
        $this->version = $version;
        $this->updateFolder = $updateFolder;
    }
    
    
    
    public function update() {
        // fetch current version
        $ctx = Context::getInstance();
        $currentVersion = (int)$ctx->getSetting($this->settingsKey, 0);
        
        if ($currentVersion >= $this->version) {
            // hmz
            return false;
        }
        
        // update folder not existing?
        if (is_dir($this->updateFolder) == false) {
            return false;
        }
        
        // create lock
        $f = $ctx->getDataDir() . '/databaseupdate.lock';
        $fp = fopen($f, 'w');
        if (!$fp) {
            throw new InvalidStateException('Unable to create database update lock');
        }
        
        
        if (flock($fp, LOCK_EX)) {
            // check version after lock
            $ctx->flushSettingCache();
            $currentVersion = (int)$ctx->getSetting($this->settingsKey, 0);
            
            if ($currentVersion < $this->version) {
                $this->doUpdate();
            }
            
            // remove lock
            flock($fp, LOCK_UN);
            fclose($fp);
            unlink($f);
        }
        
        
    }
    
    protected function doUpdate() {
        $ctx = Context::getInstance();
        
        $currentVersion = (int)$ctx->getSetting($this->settingsKey, 0);

        if (is_dir($this->updateFolder) == false) {
            // updates-folder doesn't exist? => must be on purpose, mark as updated
            $settingsService = new SettingsService();
            $settingsService->updateValue($this->settingsKey, $this->version);
            
            return false;
        }
        
        // fetch update files
        $dh = opendir( $this->updateFolder );
        if (!$dh)
            return false;
        
        $files = array();
        while ($f = readdir($dh)) {
            if (preg_match('/^\\d{10}.sql$/', $f) == false)
                continue;
            
            $ts = substr($f, 0, strpos($f, '.'));
            
            if ($ts > $currentVersion) {
                $files[] = $f;
            }
        }
        
        sort($files);
        
        // execute update
        $dbh = DatabaseHandler::getResource('default');
        foreach($files as $f) {
            $dbh->multi_query( file_get_contents( $this->updateFolder . '/' . $f) );
            
            // flush results
            while ( $dbh->more_results() ) {
                $dbh->next_result();
            }
            
            if (DEBUG == false && $dbh->error) {
                throw new MonitoringAlertException('Fout bij updaten database: ' . $dbh->error);
            }
        }
        
        // mark as updated
        $settingsService = new SettingsService();
        $settingsService->updateValue( $this->settingsKey, $this->version );
    }
    
}

