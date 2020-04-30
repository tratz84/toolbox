<?php

namespace webmail\cron;


use core\cron\CronJobBase;

class WebmailSyncJob extends CronJobBase {
    
    
    public function __construct() {
        $this->title = 'Webmail imap/pop3 synchroniseren';
    }
    
    
    public function run() {
        webmail_import_connectors(true);
    }

    public function checkJob() {
        // between 07.00 & 18.00
        // minuts, 0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55
        if (date('G') >= 7 && date('G') <= 18 && intval(date('i'))%5 == 0) {
            return true;
        }
        
        return false;
    }
    
    
}


