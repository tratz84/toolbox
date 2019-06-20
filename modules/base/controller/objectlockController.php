<?php


use core\controller\BaseController;
use base\util\ActivityUtil;

class objectlockController extends BaseController {
    
    
    
    public function action_lock() {
        
        lock_object(get_var('objectName'), get_var('id'));
        
        $prefix = get_var('prefix');
        if ($prefix)
            $prefix = $prefix . ': ';
        
        ActivityUtil::logActivityRefObject(get_var('objectName'), get_var('id'), 'object-unlock', $prefix.'Object locked');
        
        redirect(get_var('r'));
    }
    
    
    
    public function action_unlock() {
        
        unlock_object(get_var('objectName'), get_var('id'));
        
        $prefix = get_var('prefix');
        if ($prefix)
            $prefix = $prefix . ': ';
        
        ActivityUtil::logActivityRefObject(get_var('objectName'), get_var('id'), 'object-unlock', $prefix.'Object unlocked');
        
        redirect(get_var('r'));
    }
    
    
}