<?php

namespace core\cron;



class BackgroundJob {
    
    protected $description = null;
    protected $cmd;
    
    public function __construct($cmd, $description=null) {
        $this->setCmd( $cmd );
        $this->setDescription( $description );
    }
    
    public function setDescription($d) { $this->description = $d; }
    public function getDescription() { return $this->description; }
    
    public function setCmd($cmd) { $this->cmd = $cmd; }
    public function getCmd() { return $this->cmd; }
    
    
}

