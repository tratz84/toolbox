<?php


namespace webmail\mail\connector;

use webmail\model\Connector;


class BaseMailConnector {
    
    protected $connector;
    protected $blnRunning = true;
    
    
    public function __construct(Connector $connector) {
        $this->setConnector( $connector );
    }
    
    
    public function setConnector($c) { $this->connector = $c; }
    public function getConnector() { return $this->connector; }
    
    
    public function stop() {
        
    }
    
    public function poll() {
        
    }
    
    public function import() {
        
    }
    
}

