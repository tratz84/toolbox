<?php



ctx()->enableModule( 'mollie' );


module_update_handler('mollie', 20250514);

hook_loader( __DIR__.'/hook' );

