<?php




use base\model\Menu;

hook_eventbus_subscribe('base', 'MenuService::listMainMenu', function($src) {
    if (hasCapability('report', 'show-reports') == false)
        return;
    
    $menuReports = new Menu();
    $menuReports->setMenuCode('report');
    $menuReports->setIconLabelUrl('fa-signal', 'Rapportage', '/?m=report&c=report');
    $menuReports->setMenuAsFirstChild( false );
    $menuReports->setWeight(20);
    $src->add($menuReports);
    
    $widgets = list_report_dashboard_widgets();
    
    if (count($widgets) > 0) {
        $menuRd = new Menu();
        $menuRd->setMenuCode('report-list');
        $menuRd->setIconLabelUrl('fa-signal', 'Overzicht', '/?m=report&c=report');
        $menuRd->setWeight(25);
        $menuReports->addChildMenu($menuRd);
        
        
        $menuRd = new Menu();
        $menuRd->setMenuCode('report-dashboard');
        $menuRd->setIconLabelUrl('fa-dashboard', 'Dashboards', '/?m=report&c=dashboard/list');
        $menuRd->setWeight(25);
        $menuReports->addChildMenu($menuRd);
    }
    
    
});

