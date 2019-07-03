<?php


use core\controller\BaseController;
use base\service\LogService;

class objectLogController extends BaseController {
    
    public function action_index() {
        
        $this->object_name = get_var('object_name');
        $this->object_id = (int)get_var('object_id');
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    public function action_search() {
        $logService = $this->oc->get(LogService::class);
        
        $lr = $logService->search(0, 500, array('object_name' => get_var('object_name'), 'object_id' => get_var('object_id')));
        
        
        $this->json(array(
            'listResponse' => $lr
        ));
    }
    
}
