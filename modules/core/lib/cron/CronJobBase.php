<?php


namespace core\cron;

abstract class CronJobBase {
    
    protected $title = null;
    
    protected $daily = false;
    protected $timeout = null;                  // timeout in seconds before next run
    
    
    public function isDaily() { return $this->daily; }
    public function getTimeout() { return $this->timeout; }
    
    public function getTitle() { return $this->title; }
    
    public function getMessage() { return ''; }
    public function getError() { return ''; }
    public function getStatus() { return ''; }
    
    public abstract function run();
    
}

