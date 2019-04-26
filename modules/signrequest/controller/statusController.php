<?php


use core\controller\BaseController;
use signrequest\service\SignRequestService;

class statusController extends BaseController {
    
    
    public function action_view() {
        $signRequestService = $this->oc->get(SignRequestService::class);
        
        if (isset($_REQUEST['back_url']))
            $this->back_url = $_REQUEST['back_url'];
        
        $this->message = $signRequestService->readMessage($_REQUEST['id']);
        
        $this->render();
    }
    
}

