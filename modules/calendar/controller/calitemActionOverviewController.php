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
//         var_export($this->events);exit;
        
        return $this->render();
    }
    
    public function action_update_item_action() {
        
        $calendarService = object_container_get(CalendarService::class);
        
        // check if calendar_item is set
        if (isset($_REQUEST['calendar_item']) == false || is_array($_REQUEST['calendar_item']) == false) {
            return $this->json([
                'error' => true,
                'message' => 'No items'
            ]);
        }

        $resp = array();
        $resp['calendar_items'] = array();
        foreach($_REQUEST['calendar_item'] as $ci_data) {
            if ($calendarService->updateActionItem( $ci_data['id'], $ci_data['item_action'], $ci_data['start_date'] )) {
                $resp['calendar_items'][] = array(
                    'calendar_item_id' => $ci_data['id'],
                    'start_date'       => $ci_data['start_date'],
                    'item_action'      => $ci_data['item_action']
                );
            }
        }
        
        $resp['success'] = true;
        
        $this->json( $resp );
    }
    
    
    
    public function action_dashboard() {
        
    }

    public function action_dashboard_settings() {
        // ??
    }
    
    
    
    
}
