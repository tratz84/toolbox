<?php



use calendar\service\CalendarService;
use core\controller\BaseController;
use core\forms\lists\ListResponse;
use calendar\model\Calendar;
use calendar\form\CalendarForm;

class calendarController extends BaseController {
    
    
    public function init() {
        checkCapability('calendar', 'edit-calendar');
    }
    
    public function action_index() {
        
        $this->render();
    }
    
    
    public function action_search( ){
        $calendarService = $this->oc->get(CalendarService::class);
        
        $calendars = $calendarService->readAllCalendars();
        
        $list = array();
        foreach($calendars as $c) {
            $list[] = $c->getFields(array('calendar_id', 'name', 'active'));
        }
        
        
        $lr = new ListResponse(0, count($calendars), count($calendars), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }

    public function action_edit( ){
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $calendarService = $this->oc->get(CalendarService::class);
        if ($id) {
            $calendar = $calendarService->readCalendar($id);
        } else {
            $calendar = new Calendar();
        }
        
        
        $calendarForm = new CalendarForm();
        $calendarForm->bind($calendar);
        
        if (is_post()) {
            $calendarForm->bind($_REQUEST);
            
            if ($calendarForm->validate()) {
                $calendarService->saveCalendar($calendarForm);
                
                redirect('/?m=calendar&c=calendar');
            }
            
        }
        
        
        
        $this->isNew = $calendar->isNew();
        $this->form = $calendarForm;
        
        
        $this->render();
        
        
        
    }
    
    public function action_delete( ){
        $calendarService = $this->oc->get(CalendarService::class);
        
        $calendarService->deleteCalendar($_REQUEST['id']);
        
        redirect('/?m=calendar&c=calendar');
    }
    
    
}
