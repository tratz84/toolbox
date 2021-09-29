<?php



ctx()->enableModule('fail2ban');

require_once __DIR__.'/lib/functions.php';


hook_loader(__DIR__.'/hook/');

module_update_handler('fail2ban', '20210929');


