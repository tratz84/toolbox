<?php



function debug_admin_notification($str) {
    //
    if (DEBUG)
        return;
    
    if (is_standalone_installation())
        return;
    
    $url = "https://api.telegram.org/bot571922944:AAGdtKEJHEtLK7HJcWQVg5vAY-FYSNwr4K8/sendMessage?chat_id=544128223&text=" . urlencode($str);
    
    get_url($url);
}


