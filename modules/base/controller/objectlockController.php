<?php


use core\controller\BaseController;
use base\util\ActivityUtil;
use core\event\LookupObject;

class objectlockController extends BaseController {
    
    
    
    public function action_lock() {
        
        lock_object(get_var('objectName'), get_var('id'));
        
        $prefix = get_var('prefix');
        if ($prefix)
            $prefix = $prefix . ': ';
        

        // lookup companyId or personId
        $companyId = null;
        $personId = null;
        
        $lo = new LookupObject(get_var('objectName'), get_var('id'));
        if ($lo->lookup()) {
            $obj = $lo->getObject();
            if (is_object($obj) && method_exists($obj, 'getCompanyId'))
                $companyId = $obj->getCompanyId();
                if (is_object($obj) && method_exists($obj, 'getPersonId'))
                $personId = $obj->getPersonId();
        }
        
        ActivityUtil::logActivity($companyId, $personId, get_var('objectName'), get_var('id'), 'object-unlock', $prefix.' locked');
        
        redirect(get_var('r'));
    }
    
    
    
    public function action_unlock() {
        
        unlock_object(get_var('objectName'), get_var('id'));
        
        $prefix = get_var('prefix');
        if ($prefix)
            $prefix = $prefix . ': ';
        
        
        // lookup companyId or personId
        $companyId = null;
        $personId = null;
        
        $lo = new LookupObject(get_var('objectName'), get_var('id'));
        if ($lo->lookup()) {
            $obj = $lo->getObject();
            if (is_object($obj) && method_exists($obj, 'getCompanyId'))
                $companyId = $obj->getCompanyId();
            if (is_object($obj) && method_exists($obj, 'getPersonId'))
                $personId = $obj->getPersonId();
        }
        
        
        ActivityUtil::logActivity($companyId, $personId, get_var('objectName'), get_var('id'), 'object-unlock', $prefix.' unlocked');
        
        redirect(get_var('r'));
    }
    
    
}