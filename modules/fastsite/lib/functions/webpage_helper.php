<?php




function fastsite_webpage_by_id($id) {
    $webpageService = object_container_get(\fastsite\service\WebpageService::class);
    
    $w = $webpageService->readWebpage( $id );
    
    return $w;
}

function fastsite_webpage_by_code($code) {
    $webpageService = object_container_get(\fastsite\service\WebpageService::class);
    
    $w = $webpageService->readWebpageByCode( $code );
    
    return $w;
}

/**
 * @param string   $code     - code of menu item
 * @param function $callback - callback function
 */
function fastsite_webmenu($code, $callback) {
    $webmenuService = object_container_get(\fastsite\service\WebmenuService::class);
    $menus = $webmenuService->readMenusByParentCode($code, true);
    
    _fastsite_webmenu($menus, 0, $callback);
}
function _fastsite_webmenu($menus, $depth, $callback) {
    for($x=0; $x < count($menus); $x++) {
        $callback($menus[$x], $depth);
        
        $children = $menus[$x]->getChildren();
        if (count($children)) {
            _fastsite_webmenu($children, $depth+1, $callback);
        }
    }
}



