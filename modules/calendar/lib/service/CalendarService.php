<?php

namespace calendar\service;


use base\forms\FormChangesHtml;
use base\util\ActivityUtil;
use calendar\form\CalendarItemForm;
use calendar\ical\VEvent;
use calendar\model\Calendar;
use calendar\model\CalendarDAO;
use calendar\model\CalendarItem;
use calendar\model\CalendarItemDAO;
use core\service\ServiceBase;

class CalendarService extends ServiceBase {
    
    
    
    public function readAllCalendars() {
        $cDao = new CalendarDAO();
        
        return $cDao->readAll();
    }

    public function readFirstCalendar() {
        $cDao = new CalendarDAO();
        
        $cals = $cDao->readActive();
        
        if (count($cals)) {
            return $cals[0];
        } else {
            return null;
        }
    }
    
    public function readCalendar($calendarId) {
        $cDao = new CalendarDAO();
        
        return $cDao->read($calendarId);
    }
    
    public function saveCalendar($form) {
        $id = $form->getWidgetValue('calendar_id');
        if ($id) {
            $calendar = $this->readCalendar($id);
        } else {
            $calendar = new Calendar();
        }
        
        $form->fill($calendar, array('calendar_id', 'name', 'active'));
        
        if (!$calendar->save()) {
            return false;
        }
        
        return true;
    }
    
    public function deleteCalendar($id) {
        $cDao = new CalendarDAO();
        
        $cDao->markDeleted($id);
    }
    
    public function readItem($calendarItemId) {
        $ciDao = new CalendarItemDAO();
        
        return $ciDao->read($calendarItemId);
    }
    
    
    public function readItems($calendarId, $start, $end) {
        $start = format_date($start, 'Y-m-d');
        $end = format_date($end, 'Y-m-d');
        
        $ciDao = new CalendarItemDAO();
        
        return $ciDao->readByDate($calendarId, $start, $end);
    }
    
    public function readEventInstancesExploded($calendarId, $start, $end) {
        $items = $this->readItems($calendarId, $start, $end);
        
        $events = array();
        foreach($items as $i) {
            $evt = VEvent::generateByCalendarItem($i);
            $eventInstances = $evt->generateEventInstances($start, $end);
            
            if (is_array($eventInstances)) {
                $events = array_merge($events, $eventInstances);
            }
        }
        
        usort($events, function($obj1, $obj2) {
            $r = strcmp($obj1->getStartDate(), $obj2->getStartDate());
            
            if ($r == 0) {
                $t1 = $obj1->getStartTime();
                $t2 = $obj2->getStartTime();
                
                if ($t1 && !$t2) {
                    return 1;
                }
                if (!$t1 && $t2) {
                    return -1;
                }
                
                $r = strcmp($t1, $t2);
                
                if ($r != 0)
                    return $r;
                
                return strcmp($obj1->getDescription(), $obj2->getDescription());
            }
            
            return $r;
        });
        
        return $events;
    }
    
    
    public function deleteItem($calendarItemId, $editDerivedItem=null, $selectedDate=null) {
        $ci = $this->readItem($calendarItemId);
        if (!$ci) {
            return;
        }
        
        $form = new CalendarItemForm();
        $form->bind($ci);
        $fch = FormChangesHtml::formNew($form);
        
        
        $ciDao = new CalendarItemDAO();
        
        $datetime = format_date($selectedDate, 'd-m-Y');
        if ($ci->getStartTime())
            $datetime .= ' ' . $ci->getStartTime();
        
        if ($editDerivedItem) {
            // add EXDATE
            $this->addExDate($calendarItemId, $selectedDate);
            
            ActivityUtil::logActivity(null, null, 'cal__calendar_item', $ci->getCalendarItemId(), 'calendar-exdate', 'Agendapunt verwijderd: ' . $ci->getTitle() . ' ('.$datetime.')', $fch->getHtml());
        } else {
            $ciDao->delete($calendarItemId);
            
            ActivityUtil::logActivity(null, null, 'cal__calendar_item', $ci->getCalendarItemId(), 'calendar-delete', 'Agendapunt verwijderd: ' . $ci->getTitle() . ' ('.$datetime.')', $fch->getHtml());
        }
    }
    
    
    public function addExDate($calendarItemId, $selectedDate) {
        $ci = $this->readItem($calendarItemId);
        
        if (valid_date($selectedDate)) {
            $newExdate = $ci->getExdate();
            if (strlen($newExdate) > 0) {
                $newExdate .= ',';
            }
            $newExdate .= format_date($selectedDate, 'Ymd').'T000000Z';
            
            $ciDao = new CalendarItemDAO();
            $ciDao->updateExDate($ci->getCalendarItemId(), $newExdate);
        }
    }
    
    
    
    
    public function saveItem(CalendarItemForm $form) {
        $id = $form->getWidgetValue('calendar_item_id');
        if ($id) {
            $ci = $this->readItem($id);
        } else {
            $ci = new CalendarItem();
        }
        
        $isNew = $ci->isNew();
        
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew($form);
        } else {
            $oldForm = new CalendarItemForm();
            $oldForm->bind($ci);
            
            $fch = FormChangesHtml::formChanged($oldForm, $form);
        }
        
        $form->fill($ci, array('calendar_item_id', 'calendar_id', 'title', 'location', 'all_day', 'private', 'cancelled', 'start_date', 'start_time', 'end_date', 'end_time', 'recurrence_type', 'message'));

        $addExDate = false;
        
        // 'Exemplaar bewerken', (dus niet de hele-reeks?)
        if ($ci->isNew() == false && $form->getWidget('edit_derived_item')->getValue()) {
            $ci->setRefCalendarItemId( $ci->getCalendarItemId() );
            $ci->setCalendarItemId(null);
            $ci->setRecurrenceRule(null);
            $ci->setRecurrenceType(null);
            
            $addExDate = true;
        } else {
            $rrule = $form->getWidget('recurrence_type')->getRecurrenceRule();
            $ci->setRecurrenceRule($rrule);
        }

        if (!$ci->save()) {
            return false;
        }
        
        // add ex date
        if ($addExDate) {
            $this->addExDate($ci->getRefCalendarItemId(), $form->getWidgetValue('selected_date'));
        }
        
        $datetime = format_date($ci->getStartDate(), 'd-m-Y');
        if ($ci->getStartTime())
            $datetime .= ' ' . $ci->getStartTime();
        
        if ($isNew) {
            ActivityUtil::logActivity(null, null, 'cal__calendar_item', $ci->getCalendarItemId(), 'calendar-created', 'Agendapunt toegevoegd: ' . $ci->getTitle() . ' ('.$datetime.')', $fch->getHtml());
        } else {
            ActivityUtil::logActivity(null, null, 'cal__calendar_item', $ci->getCalendarItemId(), 'calendar-created', 'Agendapunt gewijzigd: ' . $ci->getTitle() . ' ('.$datetime.')', $fch->getHtml());
        }
        
        
        return true;
    }
    
    
    
    
}


