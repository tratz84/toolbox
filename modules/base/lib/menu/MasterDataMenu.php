<?php

namespace base\menu;

use core\ObjectContainer;

class MasterDataMenu {
    
    
    protected $menu = array();
    protected $sections = array();
    
    public function __construct() {
        
    }
    
    public function getMenu() {
        return $this->menu;
    }
    
    public function setSectionPrio($section, $prio) { $this->sections[$section]['prio'] = $prio; }
    public function getSectionPrio($section) {
        if (isset($this->sections[$section]) && isset($this->sections[$section]['prio']))
            return $this->sections[$section]['prio'];
        else
            return 10;
    }
    
    public function addItem($section, $title, $url, $prio=10) {
        
        if (isset($this->menu[$section]) == false) {
            $this->menu[$section] = array();
        }
        
        $this->menu[$section][] = array('title' => $title, 'url' => $url, 'prio' => $prio);
        
        if (isset($this->sections[$section]) == false) {
            $this->sections[$section] = array(
                'prio' => 10
            );
        }
    }
    
    
    public function render() {
        $html = '';
        
        $sections = array_keys($this->menu);
        
        $me = $this;
        
        usort($sections, function($n1, $n2) use ($me) {
            if ($n1 == t('Settings'))
                return -1;
                if ($n2 == t('Settings'))
                return 1;
            
            $prio1 = $me->getSectionPrio($n1);
            $prio2 = $me->getSectionPrio($n2);
            if ($prio1 != $prio2) {
                return $prio1 - $prio2;
            }
            
            return strcmp($n1, $n2);
        });
        
        foreach($sections as $s) {
            $items = $this->menu[$s];
            
            $html .= '<div class="col-xs-12 col-sm-4 col-lg-3 setting-menu-tag-container">';
            $html .= '<h2>'.esc_html($s).'</h2>';
            $html .= '<ul class="">';
            foreach($items as $i) {
                $html .= '<li><a href="'.appUrl($i['url']) . '">'.esc_html($i['title']).'</a></li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
            
            
        }
        
        return $html;
    }
    
    
    public static function generate() {
        $mdm = new MasterDataMenu();
        $mdm->addItem(t('Settings'), t('User Management'), '/?m=base&c=user');
        $mdm->addItem(t('Settings'), t('Company settings'), '/?m=base&c=masterdata/companySettings');
        $mdm->addItem(t('Settings'), t('Application settings'), '/?m=base&c=masterdata/settings');
        
        $eb = ObjectContainer::getInstance()->get(\core\event\EventBus::class);
        $eb->publishEvent($mdm, 'masterdata', 'menu');
        
        return $mdm;
    }
    
    
}
