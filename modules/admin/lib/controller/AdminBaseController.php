<?php

namespace admin\controller;


use base\model\Menu;
use core\controller\BaseController;

class AdminBaseController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function init() {
        
        // TODO: extra auth check
        
        if (is_standalone_installation() == false) {
            $this->setDecoratorFile( module_file('admin', 'templates/decorator/default.php') );
        }
    }

    
    public function loadMenu() {
        $user = $this->ctx->getUser();
        
        $this->menuItems = array();
        
        $m1 = new Menu();
        $m1->setIconLabelUrl('fa-dashboard', 'Dashboard', '/');
        $this->menuItems[] = $m1;
        
        $m15 = new Menu();
        $m15->setIconLabelUrl('fa-bar-chart', 'Rapportage', '/?m=admin&c=report');
        $this->menuItems[] = $m15;
        
        if ($user && $user->getUserType() == 'admin') {
            $m2 = new Menu();
            $m2->setIconLabelUrl('fa-user', 'Gebruikers', '/?m=admin&c=user');
            $this->menuItems[] = $m2;
        }
        if ($user && $user->getUserType() != 'admin') {
            $m2 = new Menu();
            $m2->setIconLabelUrl('fa-user', 'Instellingen', '/?m=admin&c=userSettings');
            $this->menuItems[] = $m2;
        }
        
        $m3 = new Menu();
        $m3->setIconLabelUrl('fa-bug', 'Error log', '/?m=admin&c=exception');
        $this->menuItems[] = $m3;
    }
    
    public function render() {
        $this->loadMenu();
        
        return parent::render();
    }
}

