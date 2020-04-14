<?php


use core\controller\BaseController;
use calendar\service\CalendarService;
use core\forms\lists\ListResponse;

class calitemActionOverviewController extends BaseController {
    
    
    public function action_index() {
        // fetch open/inprogress/postponed calendar items
        /** @var CalendarService $calendarService */
        $calendarService = object_container_get(CalendarService::class);
        $cal = $calendarService->readFirstCalendar();
        
        $this->calendar_id = $cal->getCalendarId();
        
        $this->map_itemActions = \calendar\model\CalendarItem::getItemActions();
        
//         var_export($this->events);exit;
        
        return $this->render();
    }
    
    
    public function action_search() {
        /** @var CalendarService $calendarService */
        $calendarService = object_container_get(CalendarService::class);
        
        $events = $calendarService->readOpenActionItems( get_var('calendar_id') );
        
        $objs = array();
        foreach($events as $evt) {
            $o = array();
            
            $o['calendar_item_id'] = $evt->getId();
            $o['recurrent'] = $evt->getRecurrent() ? true : false;
            $o['start_date'] = $evt->getStartDate();
            $o['start_time'] = $evt->getStartTime();
            $o['customerName'] = $evt->getCustomerName();
            $o['description'] = $evt->getDescription();
            $o['item_action'] = $evt->getItemAction();
            
            
            $objs[] = $o;
        }
        
        
        
        $lr = new ListResponse(0, count($objs), count($objs), $objs);
        
        return $this->json(array(
            'listResponse' => $lr
        ));
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
        $calendarService = object_container_get(CalendarService::class);
        $cal = $calendarService->readFirstCalendar();
        
        $this->calendar_id = $cal->getCalendarId();
        
        $this->map_itemActions = \calendar\model\CalendarItem::getItemActions();
        
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }

    public function action_dashboard_settings() {
        // TODO: calendar selection? now default the first calendar is selected..
    }
    
    
    
    
}
