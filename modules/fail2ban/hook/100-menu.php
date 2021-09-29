<?php







hook_eventbus_subscribe('masterdata', 'menu', function ($src) {
    
    $src->addItem('Fail2ban', t('Settings'),         '/?m=fail2ban&c=settings');
    $src->addItem('Fail2ban', t('Black/white list'), '/?m=fail2ban&c=ipsettings');
    
    $src->addItem('Fail2ban', t('Latest checks'),    '/?m=fail2ban&c=check');
    
});


