<?php


use core\controller\BaseController;
use core\exception\InvalidStateException;

class debugController extends BaseController {
    
    public function init() {
        if (!DEBUG) {
            throw new InvalidStateException('Debugging not enabled');
        }
    }
    
    
    public function action_show_debug_info() {
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
}
