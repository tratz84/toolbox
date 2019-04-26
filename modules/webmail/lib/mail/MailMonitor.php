<?php


namespace webmail\mail;


class MailMonitor {
    
    protected $connector;
    protected $blnRunning = true;
    
    
    public function setConnector($c) { $this->connector = $c; }
    public function getConnector() { return $this->connector; }
    
    
    public function stop() {
        $this->blnRunning = false;
        
    }
    
}

