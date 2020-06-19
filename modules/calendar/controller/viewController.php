<?php



use calendar\form\CalendarItemForm;
use calendar\model\CalendarItem;
use calendar\service\CalendarService;
use core\controller\BaseController;

class viewController extends BaseController {
    
    public function init() {
        checkCapability('calendar', 'edit-calendar');
        
        $this->addTitle(t('Calendar'));
    }
    
    
    public function action_index() {
        $calendarService = $this->oc->get(CalendarService::class);
        
        $this->today = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
        
        $this->calendar = $calendarService->readFirstCalendar();
        
        $this->addTitle($this->calendar->getName());
        
        $this->render();
    }
    
    
    public function action_request_items() {
        $calendarService = $this->oc->get(CalendarService::class);
        
        $items = $calendarService->readEventInstancesExploded($_REQUEST['calendarId'], $_REQUEST['startDate'], $_REQUEST['endDate']);
        
        $r = array();
        $r['events'] = $items;
        
        $this->json($r);
    }
    
    
    public function action_edit() {
        $calendarService = $this->oc->get(CalendarService::class);
        
        if (isset($_REQUEST['calendar_item_id']) && $_REQUEST['calendar_item_id']) {
            $calendarItem = $calendarService->readItem($_REQUEST['calendar_item_id']);
        } else {
            $calendarItem = new CalendarItem();
            
            if ($_REQUEST['calendarId'] == 'first') {
                $this->calendar = $calendarService->readFirstCalendar();
                $calendarItem->setCalendarId($this->calendar->getCalendarId());
            } else {
                $calendarItem->setCalendarId($_REQUEST['calendarId']);
            }
            
            if (get_var('company_id')) {
                $calendarItem->setCompanyId( get_var('company_id') );
            }
            if (get_var('person_id')) {
                $calendarItem->setPersonId( get_var('person_id') );
            }
            
            
            $calendarItem->setStartDate($_REQUEST['startDate']);
        }
        
        $this->isNew = $calendarItem->isNew();
        
        $this->form = new CalendarItemForm();
        $this->form->bind($calendarItem);
        
        if ($calendarItem->isNew()) {
            $this->form->bind($_REQUEST);
        }
        
        $edit_derived_item = isset($_REQUEST['edit_derived_item']) && $_REQUEST['edit_derived_item'] ? 1 : 0;
        $this->form->getWidget('edit_derived_item')->setValue( $edit_derived_item );
        if (isset($_REQUEST['startDate'])) {
            $this->form->getWidget('selected_date')->setValue($_REQUEST['startDate']);
            
            // derived item? & item-action set? => check/set item_action-value
            if ($this->form->getWidget('item_action')) {
                $item_action = object_meta_get( CalendarItem::class, $calendarItem->getCalendarItemId(), 'action-occurrence-'.$_REQUEST['startDate'] );
                if ($item_action) {
                   $this->form->getWidget('item_action')->setValue( $item_action );
                }
            }
        }
        
        // existing item with recurrence-rule, but editing 'Exemplaar' => set start_date to selected_date
        if ($calendarItem->isNew() == false && get_var('edit_derived_item') == true) {
            $this->form->getWidget('start_date')->setValue(format_date($_REQUEST['startDate'], 'Y-m-d'));
            $this->form->getWidget('recurrence_type')->resetValues();
        }
        
        $this->readonly = get_var('readonly') ? true : false;
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    
    
    public function action_delete() {
        $calendarService = $this->oc->get(CalendarService::class);
        
        
        $id              = (int)$_REQUEST['calendar_item_id'];
        $editDerivedItem = $_REQUEST['edit_derived_item'] ? true : false;
        $selected_date   = $_REQUEST['selected_date'];
        
        $calendarService->deleteItem($id, $editDerivedItem, $selected_date);
        
        print 'OK';
    }
    
    
    
    public function action_save() {
        $calendarService = $this->oc->get(CalendarService::class);
        
        
        $form = new CalendarItemForm();
        
        if (isset($_REQUEST['calendar_item_id']) && $_REQUEST['calendar_item_id']) {
            $calendarItem = $calendarService->readItem($_REQUEST['calendar_item_id']);
        } else {
            $calendarItem = new CalendarItem();
        }
        
        $form->bind($calendarItem);
        
        $form->bind($_REQUEST);
        
        $r = array();
        
        if ($form->validate()) {
            $calendarService->saveItem($form);
            
            $r['success'] = true;
        } else {
            $r['errors'] = $form->getErrorsForJson();
            $r['success'] = false;
        }
        
        $this->json($r);
    }
    
}


