<?php



use calendar\service\CalendarService;
use core\controller\BaseController;

class dashboardController extends BaseController {
    
    
    public function action_upcoming() {
        
        $calendarService = $this->oc->get(CalendarService::class);
        
        $cal = $calendarService->readFirstCalendar();
        
        $this->items = array();
        
        if ($cal) {
            $this->items = $calendarService->readEventInstancesExploded($cal->getCalendarId(), date('Y-m-d'), date('Y-m-d', strtotime('+37 days')));
//             $this->items = $calendarService->readEventInstancesExploded($cal->getCalendarId(), '2018-05-01', '2018-06-01');
        }
        
        
        
        $this->setShowDecorator(false);
        
        $this->render();
    }
    
    
}