<?php



namespace reportbuilder\cron;


use core\cron\CronJobBase;
use core\container\ArrayContainer;
use base\service\SettingsService;

class ReportbuilderCacheCron extends CronJobBase {
    
    
    public function __construct() {
        
        // Don't set to daily. While this cron is run daily, it must be started as one of the first, to fill-up caching
        $this->daily = false;
    }
    
    public function checkJob() {
        // after 2-o'clock?
        if (date('G') > 2) {
            $rlr = ctx()->getSetting('reportbuilder-last-run');
            
            // never run yet? or not today? => let's go!
            if (valid_datetime($rlr) == false || format_date($rlr, 'Y-m-d') != date('Y-m-d')) {
                return true;
            }
        }
        
        
        return false;
    }
    
    
    public function run() {
        $ss = object_container_get(SettingsService::class);
        $ss->updateValue('reportbuilder-last-run', date('Y-m-d H:i:s'));
        
        $ac = new ArrayContainer();
        
        hook_eventbus_publish($ac, 'reportbuilder', 'cron-cache');
        
        for($x=0; $x < $ac->count(); $x++) {
            $r = $ac->get( $x );
            
            print "Creating cache for report: " . $r->getReportName() . "\n";
            
            $r->createCache();
        }
    }

}


