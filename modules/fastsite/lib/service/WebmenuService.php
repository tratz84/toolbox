<?php

namespace fastsite\service;


use core\service\ServiceBase;
use fastsite\model\WebmenuDAO;
use core\exception\ObjectNotFoundException;

class WebmenuService extends ServiceBase {
    
    
    
    
    public function readMenu($menuId) {
        $mDao = new WebmenuDAO();
        
        $m = $mDao->read($menuId);
        
        if (!$m) {
            throw new ObjectNotFoundException('Menu not found');
        }
        
        return $m;
    }
    
    
    
}
