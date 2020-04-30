<?php


namespace core\cron;

abstract class CronJobBase {
    
    protected $title = null;
    
    protected $daily = false;
    protected $timeout = null;                  // timeout in seconds before next run
    
    // isDaily set? => run CronJobBase daily
    public function isDaily() { return $this->daily; }
    
    // timeout set? => check if "timeout"-seconds is elapsed since last run
    public function getTimeout() { return $this->timeout; }
    
    // can be overridden for custom business-rules
    public function checkJob() { return false; }
    
    public function getTitle() { return $this->title; }
    
    public function getMessage() { return ''; }
    public function getError() { return ''; }
    public function getStatus() { return ''; }
    
    public abstract function run();
    
}

