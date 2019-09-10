<?php


use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\exception\InvalidStateException;
use core\filter\FilterChain;
use core\filter\DispatchFilter;


if (is_standalone_installation() == false) {
    throw new InvalidStateException('fastsite-module not supported in multi-administration-mode');
}

if (defined('MODULE_FASTSITE'))
    return;

define('MODULE_FASTSITE', 1);


require_once dirname(__FILE__).'/lib/functions/webpage_helper.php';


Context::getInstance()->enableModule('fastsite');

$eb = ObjectContainer::getInstance()->get(EventBus::class);


$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    
    $ac = $evt->getSource();
    
    $miFastsite = new Menu();
    $miFastsite->setIconLabelUrl('fa-file-archive-o', 'Fastsite', '/?m=fastsite&c=webpage');
    $miFastsite->setMenuAsFirstChild(false);
    $ac->add($miFastsite);
    
    
    $miWebpage = new Menu();
    $miWebpage->setIconLabelUrl('fa-file-archive-o', 'Webpages', '/?m=fastsite&c=webpage');
    $miFastsite->addChildMenu($miWebpage);
    
    $miMenu = new Menu();
    $miMenu->setIconLabelUrl('fa-file-archive-o', 'Webmenu', '/?m=fastsite&c=webmenu');
    $miFastsite->addChildMenu($miMenu);
    
    $miTemplates = new Menu();
    $miTemplates->setIconLabelUrl('fa-file-archive-o', 'Templates', '/?m=fastsite&c=template/template');
    $miFastsite->addChildMenu($miTemplates);
    
}));

$eb->subscribe('core', 'pre-call-'.FilterChain::class.'::execute', new CallbackPeopleEventListener(function($evt) {
    
    if (strpos($_SERVER['REQUEST_URI'], '/backend/') !== false) {
        return;
    }
    if (strpos($_SERVER['REQUEST_URI'], BASE_HREF.'module/') !== false) {
        return;
    }
    
    $src = $evt->getSource();
    $filterChain = $src[0];
    $filterChain->clearFilters();
   
    $filterChain->addFilter( new \fastsite\filter\FastsiteSessionFilter() );
    $filterChain->addFilter( new \core\filter\ModulePublicFilter() );
    $filterChain->addFilter( new \fastsite\filter\FastsiteTemplateFilter() );
    $filterChain->addFilter( new \core\filter\DatabaseFilter() );
    $filterChain->addFilter( new \fastsite\filter\FastsiteRouteFilter() );
    $filterChain->addFilter( new DispatchFilter() );
}));

add_filter('appUrl', function($url) {
    $url = substr($url, strlen(BASE_HREF));
    
    return BASE_HREF . 'backend/' . $url;
});


