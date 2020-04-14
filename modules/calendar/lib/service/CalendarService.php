<?php

namespace calendar\service;


use base\forms\FormChangesHtml;
use base\util\ActivityUtil;
use calendar\CalendarSettings;
use calendar\form\CalendarItemForm;
use calendar\ical\VEvent;
use calendar\model\Calendar;
use calendar\model\CalendarDAO;
use calendar\model\CalendarItem;
use calendar\model\CalendarItemDAO;
use core\exception\InvalidArgumentException;
use core\exception\InvalidStateException;
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
    
    
    public function readEventInstancesExplodedByCustomer($companyId, $personId, $start=null, $end) {
        // validate
        if (!$companyId && !$personId) {
            throw new InvalidArgumentException('No company/person id set');
        }
        
        $ciDao = new CalendarItemDAO();
        $opts = array();
        if ($companyId)
            $opts['company_id'] = $companyId;
        if ($personId)
            $opts['person_id'] = $personId;
        
        if ($start == null) {
            $start = $ciDao->getFirstCalendarDate( $opts );
        }
        if ($end == null) {
            $end = date('Y-m-d', strtotime('+12 months'));
        }
        
        // no items found? => skip
        if (!$start) {
            return array();
        }
        
        $items = $ciDao->readByOpts($opts, $start, $end);
        
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
        /** @var CalendarSettings $calendarSettings */
        $calendarSettings = object_container_get(CalendarSettings::class);
        
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
        
        $form->fill($ci, array('calendar_item_id', 'calendar_id', 'customer_id', 'item_action', 'title', 'location', 'all_day', 'private', 'cancelled', 'start_date', 'start_time', 'end_date', 'end_time', 'recurrence_type', 'message'));

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
        
        // calendarItem actions enabled & no rrule set? & no ex-date selected?
        if ($calendarSettings->calendarItemActionsEnabled() && $rrule == '' && $addExDate == false) {
            $itemAction = $form->getWidgetValue('item_action');
            $ci->setItemAction( $itemAction );
        }

        if (!$ci->save()) {
            return false;
        }
        
        // add ex date
        if ($addExDate) {
            $this->addExDate($ci->getRefCalendarItemId(), $form->getWidgetValue('selected_date'));
            
            // calendarItem actions enabled? => save itemAction
            if ($calendarSettings->calendarItemActionsEnabled()) {
                $itemAction = $form->getWidgetValue('item_action');
                $selectedDate = $form->getWidgetValue('selected_date');
                object_meta_save(CalendarItem::class, $ci->getCalendarItemId(), 'action-occurrence-'.format_date($selectedDate, 'Y-m-d'), $itemAction);
            }
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
    
    
    
    public function readOpenActionItems($calendarId, $daysPast=-90, $daysFuture = 0, $opts=array()) {
        
        $start = date('Y-m-d', strtotime($daysPast . ' days'));
        $end   = date('Y-m-d', strtotime($daysFuture . ' days'));
        
        
        $events = $this->readEventInstancesExploded( $calendarId, $start, $end );
        
        // fetch status of recurrent-calendar_items
        for($x=0; $x < count($events); $x++) {
            $evt = $events[$x];

            if ( $evt->getRecurrent() ) {
                // $evt->getId() == calendar_item_id
                $action = object_meta_get( CalendarItem::class, $evt->getId(), 'action-occurrence-'.$evt->getStartDate() );
                
                if ($action) {
                    $events[$x]->setItemAction($action);
                }
                // default
                else {
                    $events[$x]->setItemAction('open');
                }
            }
        }
        
        // filter
        $events = array_filter($events, function($evt) {
            
//             if ($evt->getStartDate() == date('Y-m-d'))
//                 return true;
            
            if ($evt->getItemAction() == 'ignore')
                return false;
            
            if ($evt->getItemAction() == 'done')
                return false;
            
            if ($evt->getCancelled())
                return false;
            
            return true;
        });
        
        
        // sort
        // order: 1. inprogress, 2. open, 3. postponed.   Sorted by date/time
        usort($events, function($e1, $e2) {
            $ia1 = $e1->getItemAction();
            $ia2 = $e2->getItemAction();
            
            // inprogress @ top
            if ($ia1 == 'inprogress' && $ia2 != 'inprogress') {
                return -1;
            }
            if ($ia1 != 'inprogress' && $ia2 == 'inprogress') {
                return 1;
            }
            
            if ($ia1 == 'open' && $ia2 != 'open') {
                return -1;
            }
            if ($ia1 != 'open' && $ia2 == 'open') {
                return 1;
            }
            
            $d1 = $e1->getStartDate();
            $d2 = $e2->getStartDate();
            
            
            $c = strcmp($d1, $d2);
            if ($c != 0) {
                return $c*-1;
            }
            
            $t1 = $e1->getStartTime();
            $t2 = $e2->getStartTime();
            
            $c = strcmp($t1, $t2);
            if ($c != 0) {
                return $c;
            }
            
            // same day & time? => sort by description
            return strcmp($e1->getDescription(), $e2->getDescription());
        });
        
        return $events;
    }
    
    
    public function updateActionItem($calendarItemId, $itemAction, $date=null) {
        /** @var CalendarItem $calendarItem */
        $calendarItem = $this->readItem($calendarItemId);
        
        
        /** @var VEvent $evt */
        $evt = VEvent::generateByCalendarItem( $calendarItem );
        
        // check if CalendarItem is recurrent
        if ($evt->getRecurrent() && valid_date($date) == false) {
            throw new InvalidStateException('Recurrent item, but no date given');
        }
        
        // check if itemAction is valid
        $availableItemActions = CalendarItem::getItemActions();
        if (isset($availableItemActions[$itemAction]) == false) {
            throw new InvalidStateException('Invalid itemAction');
        }
        
        
        if ($evt->getRecurrent()) {
            $eventInstances = $evt->generateEventInstances($date, $date);
            
            if (count($eventInstances) == 0) {
                throw new InvalidStateException('CalendarItem does not occur on given date');
            }
            
            // set itemAction on recurrence instance
            object_meta_save(CalendarItem::class, $calendarItem->getCalendarItemId(), 'action-occurrence-'.format_date($date, 'Y-m-d'), $itemAction);
        }
        else {
            // set itemAction on CalendarItem
            $calendarItem->setItemAction( $itemAction );
            $calendarItem->save();
        }
        
        return true;
    }
    
    
    
    
    
}


