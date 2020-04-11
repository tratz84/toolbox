<?php


use base\model\User;
use core\controller\BaseController;

class stateController extends BaseController {
    
    
    
    
    public function action_save() {
        $key = 'js-'.get_var('key');
        $val = get_var('value');
        
        $bln = object_meta_save(User::class, ctx()->getUser()->getUserId(), $key, $val);
        
        return $this->json([
            'success' => $bln
        ]);
    }
    
    public function action_get() {
        $key = 'js-'.get_var('key');
        
        $val = object_meta_get(User::class, ctx()->getUser()->getUserId(), $key);
        
        $r = array();
        $r['success'] = $val !== null ? true : false;
        $r['value'] = $val;
        
        return $this->json( $r );
    }
    
}


