<?php



hook_eventbus_subscribe('masterdata', 'menu', function($src) {
    
    $src->addItem('Fail2ban', t('Banned networks'),       '/?m=fail2ban&c=blacklist');
    
});
