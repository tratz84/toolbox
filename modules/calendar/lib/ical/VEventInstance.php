<?php

namespace calendar\ical;

class VEventInstance {

    public $id;
    public $itemAction = null;
    public $startDate = null;
    public $endDate = null;
    public $startTime = null;
    public $endTime = null;
    public $allDay = false;
    public $cancelled = false;
    public $recurrent = false;
    
    public $customerName = null;
    public $description = null;
    
    
    public function __construct() {
        
    }
    
    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    
    public function setItemAction($action) { $this->itemAction = $action; }
    public function getItemAction() { return $this->itemAction; }
    
    public function setStartDate($date) { $this->startDate = $date; }
    public function getStartDate() { return $this->startDate; }
    public function getStartDateFormat($f='d-m-Y') { return format_date($this->startDate, $f); }

    public function setStartTime($time) {
        if (preg_match('/^\\d{2}:\\d{2}:\\d{2}$/', $time))
            $time = substr($time, 0, 5);
        $this->startTime = $time;
    }
    public function getStartTime() { return $this->startTime; }
    
    public function setEndDate($date) { $this->endDate = $date; }
    public function getEndDate() { return $this->endDate; }
    
    public function setEndTime($time) {
        if (preg_match('/^\\d{2}:\\d{2}:\\d{2}$/', $time))
            $time = substr($time, 0, 5);
        $this->endTime = $time;
    }
    public function getEndTime() { return $this->endTime; }
    
    public function setCustomerName($d) { $this->customerName = $d; }
    public function getCustomerName() { return $this->customerName; }
    
    public function setDescription($d) { $this->description = $d; }
    public function getDescription() { return $this->description; }
    
    public function setAllDay($b) { $this->allDay = $b; }
    public function getAllDay() { return $this->allDay; }
    
    public function setCancelled($b) { $this->cancelled = $b; }
    public function getCancelled() { return $this->cancelled; }
    
    public function setRecurrent($b) { $this->recurrent = $b; }
    public function getRecurrent() { return $this->recurrent; }
    
}

