<?php

namespace core\controller;

use core\controller\BaseController;


class IndexTableBaseController extends BaseController {
    
    
    protected function search() {
        
        
    }
    
    public function renderRow($row) {
        
        return $row;
    }
    
}

