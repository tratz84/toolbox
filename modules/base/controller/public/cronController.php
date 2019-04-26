<?php


use core\controller\BaseController;
use base\service\CronService;

class cronController extends BaseController {
    
    public function action_run() {
        
        $cronService = $this->oc->get(CronService::class);
        
        $cronService->runCron();
        
        print 'OK';
    }
    
}
