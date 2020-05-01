<?php

namespace webmail\cron;


use core\cron\CronJobBase;

class WebmailSyncJob extends CronJobBase {
    
    
    public function __construct() {
        $this->title = 'Webmail imap/pop3 synchroniseren';
    }
    
    
    public function run() {
        webmail_import_connectors(true);
        
        object_meta_save('webmail', null, 'last-webmail-sync', time());
    }

    public function checkJob() {
        $lastSync = object_meta_get('webmail', null, 'last-webmail-sync');
        if ($lastSync == null || (time() - (60*4.5)) > $lastSync) {
            return true;
        }
        
        return false;
    }
    
    
}


