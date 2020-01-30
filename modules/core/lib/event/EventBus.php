<?php


namespace core\event;


class EventBus {
    
    protected $subscribers = array();
    
    
    
    public function subscribe($moduleName, $actionName, $listener) {
        $key = $moduleName . '-' . $actionName;
        
        if (isset($this->subscribers[$key]) == false)
            $this->subscribers[$key] = array();
        
        $this->subscribers[$key][] = $listener;
    }
    
    public function unsubscribe(PeopleEvent $pel) {
        // TODO
    }
    
    
    public function publishEvent($source, $moduleName, $actionName, $message=null) {
        $pe = new PeopleEvent($source);
        $pe->setModuleName($moduleName);
        $pe->setActionName($actionName);
        $pe->setMessage($message);
        
        return $this->publish($pe);
    }
    
    public function publish(PeopleEvent $evt) {
        if (DEBUG && php_sapi_name() != 'cli' && in_array(get_var('c'), array('multiuser', 'ping')) == false) {
            // TODO: log event
            $_SESSION['debug']['eventbus-publish'] = $_SESSION['debug']['eventbus-publish'] ?? array();
            $_SESSION['debug']['eventbus-publish'][] = array(
                'method' => $_SERVER['REQUEST_METHOD'],
                'url' => $_SERVER['REQUEST_URI'],
                'moduleName' => $evt->getModuleName(),
                'actionName' => $evt->getActionName(),
                'message'    => $evt->getMessage()
            );
        }
        
        $mn = $evt->getModuleName();
        $an = $evt->getActionName();
        
        if ($an == null)
            $an = "defaultAction";
            
        $key = $mn . "-" . $an;
        
        
        if (isset($this->subscribers[$key]) == false) {
            return $evt;
        }
        
        foreach($this->subscribers[$key] as $s) {
            $s->peopleAction( $evt );
        }
        
        return $evt;
    }
    
    
}
