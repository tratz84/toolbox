<?php


namespace calendar;


class CalendarSettings {
    
    
    protected $calendarItemActionsEnabled = false;
    
    public function __construct() {
        $ctx = \core\Context::getInstance();
        $this->calendarItemActionsEnabled = $ctx->getSetting('calendar_item_actions_enabled', false);
    }
    
    
    public function calendarItemActionsEnabled() {
        return $this->calendarItemActionsEnabled;
    }
    
    
}

