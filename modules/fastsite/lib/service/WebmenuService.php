<?php

namespace fastsite\service;


use core\service\ServiceBase;
use fastsite\model\WebmenuDAO;
use core\exception\ObjectNotFoundException;
use fastsite\form\WebmenuForm;
use fastsite\model\Webmenu;

class WebmenuService extends ServiceBase {
    
    
    public function readMenus() {
        $wDao = new WebmenuDAO();
        
        $menus = $wDao->readAll();
        
        return $menus;
    }
    
    
    public function readByParent($parentWebpageId=null, $recursive=false) {
        $wDao = new WebmenuDAO();
        
        $items = $wDao->readByParent( $parentWebpageId );
        if ($recursive) for($x=0; $x < count($items); $x++) {
            $id = $items[$x]->getWebmenuId();
            
            $subitems = $this->readByParent($id, true);
            $items[$x]->setChildren($subitems);
        }
        
        return $items;
    }
    
    
    public function readMenu($menuId) {
        $mDao = new WebmenuDAO();
        
        $m = $mDao->read($menuId);
        
        if (!$m) {
            throw new ObjectNotFoundException('Menu not found');
        }
        
        return $m;
    }
    
    public function updateMenuSort($ids) {
        $mDao = new WebmenuDAO();
        
        $mDao->updateSort($ids);
    }
    
    public function saveWebmenu(WebmenuForm $form) {
        $wDao = new WebmenuDAO();
        
        $id = $form->getWidgetValue('webmenu_id');
        
        
        if ($id) {
            $webmenu = $wDao->read($id);
        } else {
            $webmenu = new Webmenu();
        }
        
        $form->fill($webmenu, array('webmenu_id', 'parent_webmenu_id', 'code', 'label', 'url', 'webpage_id', 'description'));
        
        $webmenu->save();
        
        return $webmenu;
    }
    
    
}
