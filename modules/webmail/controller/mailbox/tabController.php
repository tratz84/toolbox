<?php


use core\controller\BaseController;

class tabController extends BaseController {
    
    
    public function action_index() {
        
        
        $this->setShowDecorator(false);
        return $this->render();
    }
     
}

