<?php



use base\forms\UserForm;
use customer\forms\CustomerTableSelectWidget;
use core\container\ObjectHookCall;
use customer\service\CustomerService;
use customer\model\UserCompanyPerson;


hook_eventbus_subscribe('core', 'create-'.UserForm::class, function(UserForm $form) {
    
    $parent = $form->getWidget('base-settings');
    
    $w = new CustomerTableSelectWidget('customer_id');
    $w->setPrio( 55 );
    $parent->addWidget( $w );
    
});

hook_eventbus_subscribe('core', 'post-call-base\service\UserService::saveUser', function(ObjectHookCall $ohc) {
    $user = $ohc->getReturnValue();
    
    $customerId = get_var('customer_id');
    
    $customerService = object_container_get( CustomerService::class );
    $customerService->linkUser( $user->getUserId(), $customerId );
    
});


//     post-call-base\forms\UserForm::fill
hook_eventbus_subscribe('core', 'post-call-' . UserForm::class . '::bind', function(ObjectHookCall $ohc) {
    
    $form = $ohc->getObject();
    $userId = $form->getWidgetValue( 'user_id' );
    
    if (!$userId)
        return;
    
    $ucp = new UserCompanyPerson();
    if ( !$ucp->readByUserId($userId) )
        return;
    
    
    $customerId = $ucp->getCustomerId();
    if (!$customerId)
        return;
    
    $customerService = object_container_get( CustomerService::class );
    $customer = $customerService->readCustomerStrId( $customerId );
    
    if ($customer) {
        $form->getWidget('customer_id')->setValue( $ucp->getCustomerId() );
    
        $form->getWidget('customer_id')->setDefaultText( $customer->getName() );
    }
    
});



