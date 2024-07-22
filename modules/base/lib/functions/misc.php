<?php




use base\model\User;
use base\service\SettingsService;


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


function settings_update_value( $key, $val ) {
    $setService = object_container_get( SettingsService::class );
    $setService->updateValue( $key, $val );
}

// for consistency. Could also just call ctx()->getSetting( .... )
function settings_get_value( $key, $defaultValue=null ) {
    return ctx()->getSetting($key, $defaultValue);
}




function password_strength_check($pw) {
    if (strlen($pw) < 6 || strlen(trim($pw)) < 6) {
        return false;
    }
    
    $cnt=0;
    if (preg_match('/\\d+/', $pw)) {
        $cnt++;
    }
    if (preg_match('/[A-Z]+/', $pw)) {
        $cnt++;
    }
    if (preg_match('/[a-z]+/', $pw)) {
        $cnt++;
    }
    
    if ($cnt >= 3) {
        return true;
    } else {
        return false;
    }
}


function cron_daily_start_hour() {
    $h = (int)ctx()->getSetting( 'cron_daily_start_hour', 6 );
    
    if ($h < 0 || $h > 23)
        $h = 6;
    
    return $h;
}


