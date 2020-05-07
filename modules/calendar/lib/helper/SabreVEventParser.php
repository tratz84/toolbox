<?php

namespace calendar\helper;


use core\exception\InvalidArgumentException;

class SabreVEventParser {
    
    protected $content = null;
    protected $events = array();
    
    
    public function __construct($str) {
        $this->content = $str;
        
        $this->parse();
    }
    
    public function parse() {
        $obj=null;
        
        try {
            $obj = \Sabre\VObject\Reader::read( $this->content );
        } catch (\Exception $ex) { }
        
        // parse failed? => skip
        if (!$obj) {
            return false;
        }
        
        // loop through calendars & events
        $it = $obj->getIterator();
        do {
            $comp = $it->current();
            
            if ($comp->name == 'VCALENDAR') {
                /** @var \Sabre\VObject\Component\VCalendar $comp */
                
                $evt = array();
                foreach($comp->VEVENT as $vevt) {
                    $evtProp = array();
                    foreach($vevt->children() as $c) {
                        if (method_exists($c, 'getValue')) {
                            $evtProp['name'] = $c->name;
                            $evtProp['value'] = $c->getValue();
                        }
                        $evt[] = $evtProp;
                    }
                    
                    $this->events[] = $evt;
                }
            }
        } while ($it->next());
    }
    
    
    public function getEventCount() { return count($this->events); }
    
    public function getEvents() {
        return $this->events;
    }
    
    public function getEventProperty($eventNo, $propName, $defaultValue = null) {
        
        $vals = $this->getEventProperties($eventNo, $propName, $defaultValue);
        
        if (count($vals) == 0) {
            return $defaultValue;
        }
        
        return $vals[0];
    }

    public function getEventProperties($eventNo, $propName, $defaultValue = null) {
        if ($eventNo < 0 || $eventNo >= count($this->events)) {
            throw new InvalidArgumentException('Invalid event no');
        }
        
        $vals = array();
        foreach($this->events[$eventNo] as $prop) {
            if ($prop['name'] == $propName) {
                $vals[] = $prop['value'];
            }
        }
        
        return $vals;
    }
    
}

