<?php



function payment_method_list($all=false) {
    $ps = object_container_get(\payment\service\PaymentService::class);
    
    $methods = array();
    
    if ($all) {
        $methods = $ps->readAllMethods();
    } else {
        $methods = $ps->readActiveMethods();
    }
    
    return $methods;
}

function payment_method_map($all=false) {
    
    $methods = payment_method_list($all);
    $map = array();
    foreach($methods as $m) {
        $map[$m->getPaymentMethodId()] = $m->getDescription();
    }
    
    return $map;
}


