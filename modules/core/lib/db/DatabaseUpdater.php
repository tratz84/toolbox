<?php

namespace core\db;

use base\service\SettingsService;
use core\Context;
use core\exception\InvalidStateException;
use core\exception\MonitoringAlertException;

class DatabaseUpdater {
    
    
    public function update() {
        if (defined('SQL_VERSION') == false || preg_match('/^\\d{10}$/', SQL_VERSION) == false)
            throw new InvalidStateException('Invalid SQL_VERSION set');
        
        // fetch current version
        $ctx = Context::getInstance();
        $sqlVersion = (int)$ctx->getSetting('SQL_VERSION', 0);
        
        if ($sqlVersion >= SQL_VERSION) {
            // hmz
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
            $sqlVersion = (int)$ctx->getSetting('SQL_VERSION', 0);
            
            if ($sqlVersion < SQL_VERSION) {
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
        
        $sqlVersion = (int)$ctx->getSetting('SQL_VERSION', 0);

        if (is_dir(ROOT . '/updates') == false) {
            // updates-folder doesn't exist? => must be on purpose, mark as updated
            $settingsService = new SettingsService();
            $settingsService->updateValue('SQL_VERSION', SQL_VERSION);
            
            return false;
        }
        
        // fetch update files
        $dh = opendir(ROOT . '/updates');
        if (!$dh)
            return false;
        
        $files = array();
        while ($f = readdir($dh)) {
            if (preg_match('/^\\d{10}.sql$/', $f) == false)
                continue;
            
            $ts = substr($f, 0, strpos($f, '.'));
            
            if ($ts > $sqlVersion) {
                $files[] = $f;
            }
        }
        
        sort($files);
        
        // execute update
        $dbh = DatabaseHandler::getResource('default');
        foreach($files as $f) {
            $dbh->multi_query( file_get_contents(ROOT . '/updates/' . $f) );
            
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
        $settingsService->updateValue('SQL_VERSION', SQL_VERSION);
    }
    
}

