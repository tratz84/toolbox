<?php


if (defined('MODULE_FASTSITE'))
    return;

define('MODULE_FASTSITE', 1);



use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\filter\FilterChain;
use fastsite\filter\FastsiteTemplateFilter;

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
    $miTemplates->setIconLabelUrl('fa-file-archive-o', 'Templates', '/?m=fastsite&c=template');
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
   
    $filterChain->addFilter( new FastsiteTemplateFilter() );
}));

add_filter('appUrl', function($url) {
    list($startUrl, $rewrittenUrl) = $url;
    
    $base = substr($rewrittenUrl, 0, strlen($rewrittenUrl) - strlen($startUrl));
    $base .= '/backend';
    
    return $base . $startUrl;
});


