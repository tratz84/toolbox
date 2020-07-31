<?php


ctx()->enableModule('twofaauth');


module_update_handler('twofaauth', '20200731');


hook_loader(__DIR__.'/hook');

