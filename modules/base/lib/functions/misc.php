<?php




use base\model\User;


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


