<?php


use base\menu\MasterDataMenu;
use core\controller\BaseController;

class indexController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    
    public function action_index() {
        
        
        $this->mdm = MasterDataMenu::generate();
        
        $this->render();
    }
    
}