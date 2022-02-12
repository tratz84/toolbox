<?php



use core\controller\BaseController;
use base\service\CronService;
use core\exception\ObjectNotFoundException;

class cronListController extends BaseController {
    
    public function init() {
        $this->addTitle(t('Master data'));
        $this->addTitle(t('Scheduled tasks'));
    }
    
    
    public function action_index() {
        $cronService = $this->oc->get(CronService::class);
        
        $this->crons = $cronService->readCrons();
        
        $this->render();
    }
    
    public function action_popup() {
        $cronService = $this->oc->get(CronService::class);
        
        $this->cron = $cronService->readCron($_REQUEST['id']);
        
        if ($this->cron == null) {
            throw new ObjectNotFoundException('Cron not found');
        }
        
        $this->cronRuns = $cronService->readCronRuns($_REQUEST['id'], 50);
        
        $this->setShowDecorator(false);
        
        $this->render();
    }
    
}

