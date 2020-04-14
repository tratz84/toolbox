<?php

namespace calendar\ical;

use calendar\model\CalendarItem;

class VEvent extends VEventInstance {
    
    public $frequency;
    public $interval = 1;
    public $byMonth = null;              // 1 - 12
    public $byMonthDay = null;           // 1 - 28/31
    public $byDay = null;                // MO,TU,WE,TH,FR,SA
    public $bySetPos = null;             // 1 - 366 or -366 - -1
    public $exDates = null;
    
    protected $daysToNum = array('MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6, 'SU' => 7);
    
    
    public function __construct($startDate, $startTime=null, $endDate=null, $endTime=null) {
        $this->startDate = $startDate;
        $this->startTime = $startTime;
        $this->endDate = $endDate;
        $this->endTime = $endTime;
    }
    
    public function parse($recurrenceRule) {
        if (!$recurrenceRule)
            return;
        
        $tokens = explode(';', $recurrenceRule);
        
        if (count($tokens)) {
            $this->setRecurrent(true);
        }
        
        foreach($tokens as $t) {
            list($key, $val) = explode('=', $t, 2);
            
            if ($key == 'FREQ') {
                $this->setFrequency($val);
            }
            if ($key == 'INTERVAL') {
                $this->setInterval($val);
            }
            if ($key == 'BYMONTH') {
                $this->setByMonth($val);
            }
            if ($key == 'BYMONTHDAY') {
                $this->setByMonthDay($val);
            }
            if ($key == 'BYDAY') {
                $this->setByDay($val);
            }
            if ($key == 'BYSETPOS') {
                $this->setBySetPos($val);
            }
        }
    }
    
    public function setInterval($i) { $this->interval = max((int)$i, 1); }
    public function setFrequency($freq) { $this->frequency = $freq; }
    public function setByMonth($month) { $this->byMonth = $month; }
    public function setByMonthDay($monthDay) { $this->byMonthDay = $monthDay; }
    public function setByDay($day) { $this->byDay = $day; }
    public function setBySetPos($pos) { $this->bySetPos = $pos; }
    public function setExDates($dates) { $this->exDates = $dates; }
    public function getExDatesArray() {
        if (!$this->exDates)
            return array();
        
        $dates = explode(',', $this->exDates);
        
        $arr = array();
        foreach($dates as $d) {
            if (strpos($d, 'T') !== false) {
                $d = substr($d, 0, strpos($d, 'T'));
            }
            
            if (strlen($d) == 8 && is_numeric($d)) {
                $arr[] = substr($d, 0, 4) . '-' . substr($d, 4, 2) . '-' . substr($d, 6, 2); 
            }
        }
        
        
        return $arr;
    }
    
    
    protected function getFrequencyAsPeriod() {
        switch( $this->frequency ) {
            case 'DAILY' :
                return 'day';
            case 'WEEKLY' :
                return 'week';
            case 'MONTHLY' :
                return 'month';
            case 'YEARLY' :
                return 'year';
        }
        
        return '';
    }
    
    
    public static function generateByCalendarItem(CalendarItem $ci) {
        $e = new VEvent($ci->getStartDate());
        
        $e->setId( $ci->getCalendarItemId() );
        $e->setItemAction( $ci->getItemAction() );
        $e->setCustomerName( $ci->getField('customer_name') );
        $e->setDescription( $ci->getTitle() );
        $e->setLocation( $ci->getLocation() );
        $e->setStartTime( $ci->getStartTime() );
        $e->setEndDate( $ci->getEndDate() );
        $e->setEndTime( $ci->getEndTime() );
        $e->setAllDay( $ci->getAllDay() );
        $e->setCancelled( $ci->getCancelled() );
        $e->setExDates($ci->getExdate());
        
        $e->parse($ci->getRecurrenceRule());
        
        return $e;
    }
    
    
    protected function generateEventInstancesDaily($startDate, $endDate) {
        $ymdstart = (int)format_date($startDate, 'Ymd');
        $ymdend = (int)format_date($endDate, 'Ymd');
        
        $exDates = $this->getExDatesArray();
        
        $instances = array();
        
        $ymditemEnd = (int)format_date($this->getEndDate(), 'Ymd');
        
        $dt = new \DateTime($this->getStartDate());
        
        // move $dt to start of period
        while ((int)$dt->format('Ymd') < $ymdstart) {
            $dt->modify('+' . $this->interval . ' day');
        }
        
        
        while ((int)$dt->format('Ymd') <= $ymdend && (!$ymditemEnd || (int)$dt->format('Ymd') <= $ymditemEnd)) {
            // skip EXDATE's
            if (in_array($dt->format('Y-m-d'), $exDates)) {
                $dt->modify('+' . $this->interval . ' day');
                continue;
            }
            
            $i = new VEventInstance();
            $i->setId($this->getId());
            $i->setStartDate($dt->format('Y-m-d'));
            $i->setStartTime($this->getStartTime());
            $i->setEndTime($this->getEndTime());
            $i->setAllDay($this->getAllDay());
            $i->setCustomerName($this->getCustomerName());
            $i->setDescription($this->getDescription());
            $i->setLocation($this->getLocation());
            $i->setCancelled($this->getCancelled());
            $i->setRecurrent(true);
            $instances[] = $i;
            
            $dt->modify('+' . $this->interval . ' day');
        }
        
        return $instances;
    }
    
    protected function generateEventInstancesWeekly($startDate, $endDate) {
        
        $ymdstart = (int)format_date($startDate, 'Ymd');
        $ymdend = (int)format_date($endDate, 'Ymd');
        
        $exDates = $this->getExDatesArray();
        
        $instances = array();
        
        $ymditemStart = (int)format_date($this->getStartDate(), 'Ymd');
        $ymditemEnd = (int)format_date($this->getEndDate(), 'Ymd');
        
        $dt = new \DateTime($this->getStartDate());
        
        // set date to start of week
        if ($dt->format('N') > 1) {
            $dt->modify('-'.($dt->format('N')-1) . ' day');
        }
        
        // move $dt to start of period
        while ((int)$dt->format('Ymd') < $ymdstart) {
            $dt->modify('+' . $this->interval . ' week');
        }

        while ((int)$dt->format('Ymd') <= $ymdend && (!$ymditemEnd || (int)$dt->format('Ymd') <= $ymditemEnd)) {
            // skip EXDATE's
            if (in_array($dt->format('Y-m-d'), $exDates)) {
                $dt->modify('+' . $this->interval . ' week');
                continue;
            }
            
            $days = explode(',', $this->byDay);
            foreach ($days as $d) {
                if (isset($this->daysToNum[$d]) == false) continue;
                
                // move to right day
                $dayno = $this->daysToNum[$d];
                
                $dt2 = clone $dt;
                if ($dt2->format('N') != $dayno) {
                    $dt2->modify('+' . ($dayno-1) . ' day');
                }
                
                // peildatum voor startdatum?
                if ((int)$dt2->format('Ymd') < $ymditemStart)
                    continue;
                
                // peildatum na einddatum
                if ($ymditemEnd && (int)$dt2->format('Ymd') > $ymditemEnd)
                    continue;
                
                
                $i = new VEventInstance();
                $i->setId($this->getId());
                $i->setStartDate($dt2->format('Y-m-d'));
                $i->setStartTime($this->getStartTime());
                $i->setEndTime($this->getEndTime());
                $i->setAllDay($this->getAllDay());
                $i->setCustomerName($this->getCustomerName());
                $i->setDescription($this->getDescription());
                $i->setLocation($this->getLocation());
                $i->setCancelled($this->getCancelled());
                $i->setRecurrent(true);
                $instances[] = $i;
            }
            
            $dt->modify('+' . $this->interval . ' week');
        }
        
        return $instances;
    }
    
    protected function generateEventInstancesMonthly($startDate, $endDate) {
        $ymdstart = (int)format_date($startDate, 'Ymd');
        $ymdend = (int)format_date($endDate, 'Ymd');
        
        $exDates = $this->getExDatesArray();
        
        $instances = array();
        
        $ymditemStart = (int)format_date($this->getStartDate(), 'Ymd');
        $ymditemEnd = (int)format_date($this->getEndDate(), 'Ymd');
        
        $dt = new \DateTime($this->getStartDate());
        
        if ($this->byMonthDay) {
            $dt->setDate($dt->format('Y'), $dt->format('n'), $this->byMonthDay);
        }
        

        while ((int)$dt->format('Ymd') <= $ymdend && (!$ymditemEnd || (int)$dt->format('Ymd') <= $ymditemEnd)) {
            // skip EXDATE's
            if (in_array($dt->format('Y-m-d'), $exDates)) {
                $dt->modify('+' . $this->interval . ' month');
                continue;
            }
            
            // determine day-number by day-name
            if ($this->byDay && isset($this->daysToNum[$this->byDay])) {
                $pos = !$this->bySetPos ? 1 : (int)$this->bySetPos;
                if (in_array($pos, array(1, 2, 3, 4, -1)) == false) $pos = 1;
                
                $dayNo = $this->daysToNum[$this->byDay];
                
                if ($pos == -1) {
                    // look backwards
                    $dt->setDate($dt->format('Y'), $dt->format('n'), $dt->format('t'));
                    
                    while($dt->format('N') != $dayNo) {
                        $dt->modify('-1 day');
                    }
                } else {
                    // look upwards
                    $dt->setDate($dt->format('Y'), $dt->format('n'), 1);
                    
                    $cnt = 0;
                    if ($dt->format('N') == $dayNo)
                        $cnt++;
                    while ($cnt < $pos) {
                        $dt->modify('+1 day');
                        if ($dt->format('N') == $dayNo) {
                            $cnt++;
                        }
                    }
                }
            }
            
            if ((int)$dt->format('Ymd') >= $ymdstart && (int)$dt->format('Ymd') <= $ymdend) {
                $i = new VEventInstance();
                $i->setId($this->getId());
                $i->setStartDate($dt->format('Y-m-d'));
                $i->setStartTime($this->getStartTime());
                $i->setEndTime($this->getEndTime());
                $i->setAllDay($this->getAllDay());
                $i->setCustomerName($this->getCustomerName());
                $i->setDescription($this->getDescription());
                $i->setLocation($this->getLocation());
                $i->setCancelled($this->getCancelled());
                $i->setRecurrent(true);
                $instances[] = $i;
            }
            
            $dt->modify('+' . $this->interval . ' month');
            
            
            // last month? => match end date
            if ($dt->format('Ym') == substr($ymdend, 0, 6) && !$this->byMonthDay) {
                $day = substr($ymdend, 6);
                $dt->setDate($dt->format('Y'), $dt->format('n'), $day);
            }
            
        }
        
        
        return $instances;
    }
    
    protected function generateEventInstancesYearly($startDate, $endDate) {
        $ymdstart = (int)format_date($startDate, 'Ymd');
        $ymdend = (int)format_date($endDate, 'Ymd');
        
        $exDates = $this->getExDatesArray();
        
        $instances = array();
        
        $ymditemEnd = (int)format_date($this->getEndDate(), 'Ymd');
        
        $dt = new \DateTime($this->getStartDate());
        
        
        while ((int)$dt->format('Ymd') <= $ymdend && (!$ymditemEnd || (int)$dt->format('Ymd') <= $ymditemEnd)) {
            
            // set by month / daynr
            if ($this->byMonth && $this->byMonthDay) {
                $dt->setDate($dt->format('Y'), $this->byMonth, $this->byMonthDay);
            }
            
            
            // set by month / day-name & position (1-4 or -1)
            if ($this->byMonth && $this->byDay && isset($this->daysToNum[$this->byDay])) {
                
                $pos = in_array((int)$this->bySetPos, array(1, 2, 3, 4, -1)) ? (int)$this->bySetPos : 1;
                
                $dayNo = $this->daysToNum[$this->byDay];
                
                if ($pos == -1) {
                    $dt->setDate($dt->format('Y'), $this->byMonth, $dt->format('t'));
                    
                    while($dt->format('N') != $dayNo) {
                        $dt->modify('-1 day');
                    }
                } else {
                    $dt->setDate($dt->format('Y'), $this->byMonth, $this->byMonthDay);
                    
                    $cnt = 0;
                    if ($dt->format('N') == $dayNo)
                        $cnt++;
                    while ($cnt < $pos) {
                        $dt->modify('+1 day');
                        if ($dt->format('N') == $dayNo) {
                            $cnt++;
                        }
                    }
                }
            }
            
            
            // skip EXDATE's
            if (in_array($dt->format('Y-m-d'), $exDates)) {
                $dt->modify('+' . $this->interval . ' year');
                continue;
            }
            
            // only current period
            if ((int)$dt->format('Ymd') >= $ymdstart && (int)$dt->format('Ymd') <= $ymdend) {
                $i = new VEventInstance();
                $i->setId($this->getId());
                $i->setStartDate($dt->format('Y-m-d'));
                $i->setStartTime($this->getStartTime());
                $i->setEndTime($this->getEndTime());
                $i->setAllDay($this->getAllDay());
                $i->setCustomerName($this->getCustomerName());
                $i->setDescription($this->getDescription());
                $i->setCancelled($this->getCancelled());
                $i->setRecurrent(true);
                $instances[] = $i;
            }
            
            $dt->modify('+' . $this->interval . ' year');
        }
        
        return $instances;
    }
    
    
    public function generateEventInstances($startDate, $endDate) {
        $ymdstart = (int)format_date($startDate, 'Ymd');
        $ymdend = (int)format_date($endDate, 'Ymd');
        
        
        if (!$this->getRecurrent()) {
            $ymd = (int)str_replace('-', '', $this->getStartDate());
            
            if ($ymdstart <= $ymd && $ymdend >= $ymd) {
                return array($this);
            }
        }
        
        // generate recurrent items
        $period = $this->getFrequencyAsPeriod();
        if (!$period) {
            return false;
        }
        
        if ($this->frequency == 'DAILY') {
            return $this->generateEventInstancesDaily($startDate, $endDate);
        }
        if ($this->frequency == 'WEEKLY') {
            return $this->generateEventInstancesWeekly($startDate, $endDate);
        }
        if ($this->frequency == 'MONTHLY') {
            return $this->generateEventInstancesMonthly($startDate, $endDate);
        }
        if ($this->frequency == 'YEARLY') {
            return $this->generateEventInstancesYearly($startDate, $endDate);
        }
        
        // hmz..
        if ($ymdstart <= $ymd && $ymdend >= $ymd) {
            return array($this);
        } else {
            return array();
        }
    }
    
}

