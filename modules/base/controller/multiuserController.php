<?php


use core\controller\BaseController;
use base\service\MultiuserService;

class multiuserController extends BaseController {
    
    
    public function action_index() {
        
        $muService = $this->oc->get(MultiuserService::class);
        
        $username = $this->ctx->getUser()->getUsername();
        
        if (get_var('key')) {
            $muService->setLock($username, get_var('tab'), get_var('key'));
        } else {
            $muService->resetLock($username, get_var('tab'));
        }
        
        
        
        $r = array();
        $r['status'] = 'OK';
        if (get_var('key')) {
            $r['locks'] = $muService->lockKeyCount(get_var('key'));
        }
        
        $this->json($r);
    }
    
}

