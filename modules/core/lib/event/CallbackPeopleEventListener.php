<?php



namespace core\event;

class CallbackPeopleEventListener extends PeopleEventListener {
    
    protected $callback=null;
    
    public function __construct($callback=null) {
        $this->callback = $callback;
    }
    
    
    public function setCallback($c) { $this->callback = $c; }
    public function getCallback() { return $this->callback; }
    
    public function peopleAction($evt) {
        // on purpose no null-check, let it crash on error..
//         $this->callback( $evt );
        call_user_func($this->callback, $evt);
    }
    
    
    
}