<?php


use core\controller\BaseController;
use base\form\CronSettingsForm;
use base\service\SettingsService;

class cronSettingsController extends BaseController {
    
    
    public function init() {
        $this->addTitle(t('Master data'));
        $this->addTitle(t('Scheduled tasks settings'));
    }
    
    
    public function action_index() {
        
        
        $this->form = new CronSettingsForm();
        
        $data['cron_daily_start_hour'] = cron_daily_start_hour();
        
        $this->form->bind( $data );
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            $setService = object_container_get( SettingsService::class );
            $setService->updateValue( 'cron_daily_start_hour', $this->form->getWidgetValue('cron_daily_start_hour') );
            
            
            report_user_message( t('Changes saved') );
            redirect( '/?m=base&c=cron/cronSettings' );
        }
        
        
        
        return $this->render();
    }
    
}


