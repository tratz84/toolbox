<?php


use core\controller\BaseController;
use calendar\service\CalendarService;

class calitemActionOverviewController extends BaseController {
    
    
    public function action_index() {
        // fetch open/inprogress/postponed calendar items
        /** @var CalendarService $calendarService */
        $calendarService = object_container_get(CalendarService::class);
        $cal = $calendarService->readFirstCalendar();
        $this->events = $calendarService->readOpenActionItems( $cal->getCalendarId() );
        
        return $this->render();
    }
    
    public function action_update_item_action() {
        
    }
    
    
    
    public function action_dashboard() {
        
    }

    public function action_dashboard_settings() {
        // ??
    }
    
    
    
    
}
