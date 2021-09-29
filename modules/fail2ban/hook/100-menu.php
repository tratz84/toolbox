<?php







hook_eventbus_subscribe('masterdata', 'menu', function ($src) {
    
    $src->addItem('Fail2ban', t('Settings'), '/?m=fail2ban&c=settings');
    $src->addItem('Fail2ban', t('Lists'),    '/?m=fail2ban&c=lists');
    
});


