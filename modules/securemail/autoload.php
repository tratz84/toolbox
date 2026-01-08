<?php

use base\model\Menu;
use core\event\CallbackPeopleEventListener;
use securemail\cron\SecretMessageCron;

require_once __DIR__.'/lib/functions/encryption.php';


ctx()->enableModule('securemail');

hook_loader(__DIR__.'/hook/');

module_update_handler('securemail', 20260107);



hook_eventbus_subscribe('base', 'MenuService::listMainMenu', function($ac) {
        
    $miMessage = new Menu();
    $miMessage->setIconLabelUrl('fa-lock', t('Secure messages'), '/?m=securemail&c=secureMessage');
    $miMessage->setWeight( 75 );
    $ac->add( $miMessage );
    
});



hook_eventbus_subscribe('croncontainer', 'init', function($cronContainer) {
    $cronContainer->addCronjob( new SecretMessageCron() );
});


