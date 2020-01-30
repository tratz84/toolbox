<?php



function debug_admin_notification($str) {
 
    // publish
    
    hook_eventbus_publish(null, 'base', 'debug-admin-notification', $str);
    
}


