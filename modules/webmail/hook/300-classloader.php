<?php


spl_autoload_register(function($name) {
    
    if ($name == 'Swift_MailTransport') {
        require_once __DIR__.'/../lib/3rdparty/Swift/MailTransport.php';
    }
    else if ($name == 'Swift_Transport_MailTransport') {
        require_once __DIR__.'/../lib/3rdparty/Swift/Transport/MailTransport.php';
    }
    
    print "$name\n";
    
});
