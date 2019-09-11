<?php

namespace fastsite\service;


use core\service\ServiceBase;
use fastsite\model\WebmenuDAO;
use core\exception\ObjectNotFoundException;
use fastsite\form\WebmenuForm;
use fastsite\model\Webmenu;
use core\exception\InvalidStateException;

class WebmenuService extends ServiceBase {
    
    
    public function readMenus() {
        $wDao = new WebmenuDAO();
        
        $menus = $wDao->readAll();
        
        return $menus;
    }
    

    public function readMenusByParentCode($parentMenuCode=null) {
        $wDao = new WebmenuDAO();
        
        $item = $wDao->readByCode( $parentMenuCode );
        $id = $item->getWebmenuId();
        
        $subitems = $this->readMenusByParent($id, true);
        $item->setChildren($subitems);
        
        return $item->getChildren();
    }
    
    public function readMenusByParent($parentMenuId=null, $recursive=false) {
        $wDao = new WebmenuDAO();
        
        $items = $wDao->readByParent( $parentMenuId );
        if ($recursive) for($x=0; $x < count($items); $x++) {
            $id = $items[$x]->getWebmenuId();
            
            $subitems = $this->readMenusByParent($id, true);
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
    
    public function deleteMenu($menuId) {
        $webmenu = $this->readMenu( $menuId );
        
        $submenus = $this->readMenusByParent( $webmenu->getWebmenuId() );
        
        if (count($submenus) > 0) {
            throw new InvalidStateException('Menu-item contains submenus');
        }
        
        return $webmenu->delete();
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
