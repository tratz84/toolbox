<?php

namespace base\util;

use base\model\Activity;
use core\Context;

class ActivityUtil {
    
    
    public static function logActivityUser($userId, $username, $code, $shortDescription=null, $longDescription=null, $changes=null, $note=null) {
        $a = new Activity();
        
        $a->setUserId($userId);
        $a->setUsername($username);
        $a->setCode($code);
        $a->setShortDescription($shortDescription);
        $a->setLongDescription($longDescription);
        $a->setNote($note);
        $a->setChanges($changes);
        
        return $a->save();
        
    }
    
    public static function logActivity($companyId, $personId, $refObject, $refId, $code, $shortDescription=null, $longDescription=null, $changes=null, $note=null) {
        $a = new Activity();
        
        $user = Context::getInstance()->getUser();
        if ($user) {
            $a->setUserId($user->getUserId());
            $a->setUsername($user->getUsername());
        }
        
        $a->setCompanyId($companyId);
        $a->setPersonId($personId);
        $a->setRefObject($refObject);
        $a->setRefId($refId);
        $a->setCode($code);
        $a->setShortDescription($shortDescription);
        $a->setLongDescription($longDescription);
        $a->setNote($note);
        $a->setChanges($changes);
        
        return $a->save();
    }

    public static function logActivityCompany($companyId, $refObject, $refId, $code, $shortDescription=null, $longDescription=null, $changes=null) {
        return self::logActivity($companyId, null, $refObject, $refId, $code, $shortDescription, $longDescription, $changes);
    }

    public static function logActivityPerson($personId, $refObject, $refId, $code, $shortDescription=null, $longDescription=null, $changes=null) {
        return self::logActivity(null, $personId, $refObject, $refId, $code, $shortDescription, $longDescription, $changes);
    }
    
    public static function logActivityRefObject($refObject, $refId, $code, $shortDescription=null, $longDescription=null, $changes=null) {
        return self::logActivity(null, null, $refObject, $refId, $code, $shortDescription, $longDescription, $changes);
    }
    
}


