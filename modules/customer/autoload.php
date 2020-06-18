<?php




ctx()->enableModule('customer');


require_once __DIR__.'/lib/functions/misc.php';
require_once __DIR__.'/lib/functions/person.php';


hook_register_javascript('select-person-list-edit', '/module/customer/js/select-person-edit-list.js');
hook_register_javascript('select-company-list-edit', '/module/customer/js/select-company-edit-list.js');


hook_eventbus_subscribe('report', 'menu-list', function($reportMenuList) {
    /**
     * report\ReportMenuList
     */
    
    $reportMenuList->addMenuItem('Klantenoverzicht', 'customer', 'report/customerReportController', '/?m=customer&c=report/customerReport&a=xls');
    
});
    
    
// customers handles as one? => redirect links to company-/person-overview to customer-overview
if (ctx()->isCustomersSplit() == false) {
    add_filter('appUrl', function($url) {
        if (endsWith($url, '/?m=customer&c=company') || endsWith($url, '/?m=customer&c=person')) {
            return appUrl('/?m=customer&c=customer');
        }
        
        return $url;
    });
}
