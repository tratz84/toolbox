<?php


hook_eventbus_subscribe('customer', 'company-edit-footer', function( $ftc ) {
    if (!hasCapability('webmail', 'send-mail'))
        return;
    
    $companyId = $ftc->getSource()->getWidgetValue('company_id');
    
    // new Company? => skip
    if (!$companyId)
        return;

    $lbl = 'Mail';
    if (ctx()->isExperimental())
        $lbl = 'Outbox';
    
    $webmailHtml = get_component('webmail', 'outbox/outboxTabController', 'index', array('companyId' => $companyId));
    $ftc->addTab( $lbl, $webmailHtml, 81, ['name' => 'mail-outbox'] );
});

    
hook_eventbus_subscribe('customer', 'person-edit-footer', function( $ftc ) {
    if (!hasCapability('webmail', 'send-mail'))
        return;
        
    $personId = $ftc->getSource()->getWidgetValue('person_id');
    
    // new Person? => skip
    if (!$personId)
        return;
    
    $lbl = 'Mail';
    if (ctx()->isExperimental())
        $lbl = 'Outbox';
        
    $webmailHtml = get_component('webmail', 'outbox/outboxTabController', 'index', array('personId' => $personId));
    $ftc->addTab( $lbl, $webmailHtml, 81, ['name' => 'mail-outbox'] );
});


