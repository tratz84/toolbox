<?php


use core\controller\BaseController;
use core\container\ArrayContainer;

class filetemplatesController extends BaseController {
    
    
    public function action_index() {
        
        $ac = new ArrayContainer();
        hook_eventbus_publish($ac, 'filesync', 'filetemplates');
        
        $this->filetemplates = $ac;
        
        
        return $this->render();
    }
    
    
}

