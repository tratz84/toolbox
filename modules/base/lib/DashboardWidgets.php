<?php

namespace base;

class DashboardWidgets {
    
    public $widgets = array();
    public $userWidgets = array();
    
    
    public function getWidgets() { return $this->widgets; }
    public function getUserWidgets() { return $this->userWidgets; }
    
    public function removeWidget( $widgetCode, $callRemoveUserWidget =true ) {
        
        $this->widgets = array_filter($this->widgets, function($w) use ($widgetCode) {
            if ( $w['code'] == $widgetCode )
                return false;
            else
                return true;
        });
        
        if ($callRemoveUserWidget)
            $this->removeUserWidget( $widgetCode, false );
    }
    
    public function removeUserWidget( $widgetCode, $callRemoveWidget=true ) {
        if (isset($this->userWidgets[$widgetCode]))
            unset( $this->userWidgets[$widgetCode] );
        
        if ($callRemoveWidget)
            $this->removeWidget( $widgetCode, false );
    }
    
    
    
    public function addWidget($code, $name, $description, $ajaxUrl) {
        $this->widgets[] = array(
            'code' => $code,
            'name' => $name,
            'description' => $description,
            'ajaxUrl' => $ajaxUrl
        );
    }
    
    public function addUserWidget($code, $x, $y, $width, $height) {
        $found = false;
        foreach($this->widgets as $w) {
            if ($w['code'] == $code) {
                $found = true;
                break;
            }
        }
        
        if ($found) {
            $this->userWidgets[$code] = array(
                'x' => $x,
                'y' => $y,
                'width' => $width,
                'height' => $height
            );
        }
        
    }
}

