<?php




use base\model\Menu;

hook_eventbus_subscribe('base', 'MenuService::listMainMenu', function($src) {
    if (hasCapability('report', 'show-reports') == false)
        return;
    
    $menuReports = new Menu();
    $menuReports->setMenuCode('report');
    $menuReports->setIconLabelUrl('fa-signal', 'Rapportage', '/?m=report&c=report');
    $menuReports->setWeight(20);
    $src->add($menuReports);
    
    $menuRd = new Menu();
    $menuRd->setMenuCode('report-dashboard');
    $menuRd->setIconLabelUrl('fa-dashboard', 'Rapportage dashboard', '/?m=report&c=dashboard/list');
    $menuRd->setWeight(25);
    $menuReports->addChildMenu($menuRd);
    
    
});

