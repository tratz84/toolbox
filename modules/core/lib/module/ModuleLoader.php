<?php

namespace core\module;


use base\model\Menu;

class ModuleLoader {
    
    protected $moduleMetas;
    protected $autoloadFile;
    protected $modulePath;
    
    public function __construct($moduleMetas, $autoload_file) {
        $this->moduleMetas = $moduleMetas;
        $this->autoloadFile = $autoload_file;
        $this->modulePath = dirname($this->autoloadFile);
    }
    
    
    public function load() {
        load_php_file( $this->autoloadFile );
        
        
        $this->loadMenus();
        
        $this->loadUserCapabilities();
        
        $this->loadMenuItems();
    }
    
    protected function loadMenus() {
        $file = $this->modulePath . '/config/mainmenu.php';
        if (file_exists($file) == false)
            return;
        
        hook_eventbus_subscribe('base', 'MenuService::listMainMenu', function($acMenu) use ($file) {
            $arr = include $file;
            
            foreach($arr as $menu) {
                $m = new Menu();
                $m->setIcon($menu['icon']);
                $m->setLabel($menu['label']);
                $m->setUrl($menu['url']);
                $m->setWeight($menu['weight']);
                
                $acMenu->add($m);
            }
        });
    }
    
    
    protected function loadUserCapabilities() {
        $file = $this->modulePath . '/config/usercapabilities.php';
        if (file_exists($file) == false)
            return;
        
        $moduleName = basename($this->modulePath);
        
        hook_eventbus_subscribe('base', 'user-capabilities', function($ucc) use ($file, $moduleName) {
            /**
             * @var $ucc base\user\UserCapabilityContainer
             */
            
            $capabilities = include $file;
            
            foreach($capabilities as $cap) {
                $ucc->addCapability($moduleName, $cap['capability_code'], $cap['short_description'], $cap['infotext']);
            }
            
        });
    }
    
    protected function loadMenuItems() {
        $menufile = $this->modulePath . '/config/codegen/menu.php';
        if (file_exists($menufile) == false)
            return;
        
        hook_eventbus_subscribe('base', 'MenuService::listMainMenu', function($src) use ($menufile) {
            $data = include $menufile;
            
            foreach($data['menu'] as $menu) {
                $data = @$menu['data'];
                
                $m = new Menu();
                $m->setIconLabelUrl(@$data['icon'], @$data['label'], @$data['url']);
                $m->setWeight( @$data['weight'] );
                $m->setMenuAsFirstChild(@$data['as_first_child']  ? true : false);
                
                // submenu's
                // TODO: recursive (?)
                if (isset($menu['children'])) foreach($menu['children'] as $cmi) {
                    $cm = new Menu();
                    $cm->setIconLabelUrl(@$cmi['data']['icon'], @$cmi['data']['label'], @$cmi['data']['url']);
                    $cm->setWeight( @$cmi['data']['weight'] );
                    $cm->setMenuAsFirstChild(@$cmi['data']['as_first_child']  ? true : false);
                    
                    $m->addChildMenu( $cm );
                }
                $src->add( $m );
            }
        });
        
    }
    
    
}
