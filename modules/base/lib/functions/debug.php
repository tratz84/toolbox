<?php



function debug_admin_notification($str) {
    //
    if (DEBUG)
        return;
    
    if (is_standalone_installation())
        return;
 
    // TODO: some event...
}


