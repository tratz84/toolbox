<?php


if (is_web()) {
    if ( strpos(request_uri_no_params(), appUrl('/mollie/return-cancel')) === 0 ) {
        ctx()->setModule( 'mollie' );
        ctx()->setController( 'public/molliehook' );
        ctx()->setAction( 'return_cancel' );
    }
    else if ( strpos(request_uri_no_params(), appUrl('/mollie/return')) === 0 ) {
        ctx()->setModule( 'mollie' );
        ctx()->setController( 'public/molliehook' );
        ctx()->setAction( 'return' );
    }
    else if ( strpos(request_uri_no_params(), appUrl('/mollie/webhook')) === 0 ) {
        ctx()->setModule( 'mollie' );
        ctx()->setController( 'public/molliehook' );
        ctx()->setAction( 'webhook' );
    }
}




hook_eventbus_subscribe('masterdata', 'menu', function($src) {
    
    
    $src->addItem('Mollie', t('Settings'),          '/?m=mollie&c=settings');
    
    
});

