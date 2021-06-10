<?php



use core\controller\BaseController;

class manifestController extends BaseController {
    
    
    public function action_index() {
        
        
        $this->setShowDecorator( false );
        
        header('Content-type: application/manifest+json');
        return $this->render();
    }
    
    
}


