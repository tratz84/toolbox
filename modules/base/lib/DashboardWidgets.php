<?php

namespace base;

class DashboardWidgets {
    
    public $widgets = array();
    public $userWidgets = array();
    
    
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

