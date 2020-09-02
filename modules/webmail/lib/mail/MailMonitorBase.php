<?php

namespace webmail\mail;

abstract class MailMonitorBase {
    
    
    public abstract function stop();
    public abstract function poll();
    public abstract function import();
    
    
    
}

