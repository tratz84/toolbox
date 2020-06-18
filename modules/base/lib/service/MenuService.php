<?php


namespace base\service;

use base\model\MenuDAO;
use core\Context;
use core\service\ServiceBase;
use core\event\EventBus;
use core\container\ArrayContainer;

class MenuService extends ServiceBase {
    
    /**
     * listMainMenu() - returns menu item's for current user
     * 
     */
    public function listMainMenu() {
        
        
        $ctx = Context::getInstance();
        
        // TODO: read menu's based on loaded modules through an EventBus-event
        
        $mDao = new MenuDAO();
        $menus = $mDao->readVisible();
        
        
        // TODO: future, check if user has permission to module/item
        $allowedMenus = array();
        foreach($menus as $m) {
            
            $enabledMenus = array();
            
            if ($ctx->isModuleEnabled('base')) {
                $enabledMenus[] = 'dashboard';
                
                if ($ctx->isCustomersSplit())
                    $enabledMenus[] = 'company';
                
                $enabledMenus[] = 'masterdata';
                
                if ($ctx->isCustomersSplit())
                    $enabledMenus[] = 'person';
                
                if (!$ctx->isCustomersSplit())
                    $enabledMenus[] = 'customer';
            }
            
            if ($ctx->isModuleEnabled('invoice')) {
                if ($ctx->getSetting('invoiceModuleEnabled')) {
                    $enabledMenus[] = 'offer';
                    $enabledMenus[] = 'invoice';
                }
                
                if ($ctx->getSetting('invoiceModuleEnabled') && $ctx->isExperimental()) {
                    $enabledMenus[] = 'to-bill';
                }
                
            }
            
            if ($ctx->isModuleEnabled('calendar')) {
                $enabledMenus[] = 'calendar';
            }
            
            if ($ctx->isModuleEnabled('project')) {
                $enabledMenus[] = 'project';
            }
            
            
            if ($ctx->isModuleEnabled('rental')) {
                $enabledMenus[] = 'rental';
                $enabledMenus[] = 'rentallist';
                $enabledMenus[] = 'rentalcontracts';
                $enabledMenus[] = 'rentalconfig';
            }
            
            if ($ctx->isModuleEnabled('report')) {
                $enabledMenus[] = 'report';
            }
            
            if ($ctx->isModuleEnabled('webmail')) {
                $enabledMenus[] = 'webmail';
            }

            if ($ctx->isModuleEnabled('filesync')) {
                $enabledMenus[] = 'filesync';
            }
            
            if ($ctx->isModuleEnabled('support')) {
                $enabledMenus[] = 'support';
            }
            
            if (in_array($m->getMenuCode(), $enabledMenus)) {
                $allowedMenus[] = $m;
            }
        }
        
        $ac = new ArrayContainer($allowedMenus);
        
        $eb = $this->oc->get(EventBus::class);
        $eb->publishEvent($ac, 'base', 'MenuService::listMainMenu');
        $ac->sort('weight');
        $items = $ac->getItems();
        
        return $items;
    }
    
}

