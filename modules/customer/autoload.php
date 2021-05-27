<?php




use core\db\DatabaseHandler;

ctx()->enableModule('customer');


// init customer__country-table on activation
hook_eventbus_subscribe('customer', 'module-update-executed', function($null) {
    include __DIR__.'/config/base_sql.php';
    
    $mysqlcon = DatabaseHandler::getConnection('default');
    
    $countryCount = $mysqlcon->queryValue('select count(*) from customer__country');
    
    if ($countryCount == 0) {
        foreach($sql_country as $sc) {
            $mysqlcon->query( $sc );
        }
    }
    
});

module_update_handler('customer', '20200630');


require_once __DIR__.'/lib/functions/misc.php';
require_once __DIR__.'/lib/functions/person.php';


hook_register_javascript('mod-customer-script',  appUrl('/?mpf=/module/customer/js/script.js'));
hook_htmlscriptloader_enableGroup('mod-customer-script');

hook_register_javascript('select-person-list-edit',  appUrl('/?mpf=/module/customer/js/select-person-edit-list.js'));
hook_register_javascript('select-company-list-edit', appUrl('/?mpf=/module/customer/js/select-company-edit-list.js'));
hook_register_javascript('customer-select-widget',   appUrl('/?mpf=/module/customer/js/customer-select-widget.js'));


hook_eventbus_subscribe('report', 'menu-list', function($reportMenuList) {
    /**
     * report\ReportMenuList
     */
    
    $reportMenuList->addMenuItem(t('Customer overview'), 'customer', 'report/customerReportController', '/?m=customer&c=report/customerReport&a=xls');
    
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
