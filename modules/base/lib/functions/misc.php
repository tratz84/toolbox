<?php




use base\model\User;
use core\db\DBObject;

function format_customername($obj) {
    if (is_object($obj)) {
        if (is_a($obj, \base\model\Person::class)) {
            return format_personname( $obj );
        } else if (is_a($obj, \base\model\Company::class)) {
            return $obj->getCompanyName();
        } else if (is_a($obj, DBObject::class)) {
            $obj = $obj->getFields();
        }
    }
    if (is_array($obj)) {
        if (isset($obj['company_id']) && $obj['company_id']) {
            return $obj['company_name'];
        } else if (isset($obj['person_id']) && $obj['person_id']) {
            
            return format_personname($obj);
        }
    }
    
    return null;
}



function getJsState($key, $defaultValue=null, $userId=null) {
    if ($userId == null) {
        $userId = ctx()->getUser()->getUserId();
    }
    
    $val = object_meta_get(User::class, $userId, 'js-'.$key);
    if ($val !== null) {
        return $val;
    } else {
        return $defaultValue;
    }
}


