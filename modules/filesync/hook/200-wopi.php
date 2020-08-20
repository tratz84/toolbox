<?php




hook_eventbus_subscribe('core', 'pre-filter-executed', function( $filter ) {
    if (is_a($filter, \core\filter\DispatchFilter::class) && strpos($_SERVER['REQUEST_URI'], appUrl('/filesync/wopi/')) === 0) {
        // note, this overrides authentication-stuff !!
        ctx()->setModule('filesync');
        ctx()->setController('public/wopi');
        ctx()->setAction('index');
    }
});
    


