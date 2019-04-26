<?php

namespace core\container;


use core\event\EventBus;
use core\ObjectContainer;
use core\event\PeopleEvent;
use core\cron\CronBase;
use core\cron\CronJobBase;

class CronContainer {
    
    protected $cronjobs = array();
    
    public function __construct() {
        
    }
    
    public function init() {
        $eb = ObjectContainer::getInstance()->get(EventBus::class);
        
        $pe = new PeopleEvent($this);
        $pe->setModuleName('croncontainer');
        $pe->setActionName('init');
        $eb->publish($pe);
    }
    
    
    public function getCronjobs() { return $this->cronjobs; }
    
    public function addCronjob(CronJobBase $cronjob) {
        $this->cronjobs[] = $cronjob;
    }
    
}

