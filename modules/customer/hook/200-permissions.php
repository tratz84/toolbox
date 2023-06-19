<?php



hook_eventbus_subscribe('base', 'user-capabilities', function($ucc) {
    /** @var \base\user\UserCapabilityContainer $ucc */
    
    $ucc->addCapability( 'customer', 'view', t('View Customers') );
    $ucc->addCapability( 'customer', 'edit', t('Edit Customers') );
    
});


hook_eventbus_subscribe('core', 'has-capability', function($cc) {
    /** @var \core\event\CapabilityEvent $cc */
    if ( $cc->getModuleName() == 'customer' && $cc->getCapabilityCode() == 'view' ) {
        if (hasCapability('customer', 'edit')) {
            $cc->setResult( true );
        }
    }
});
