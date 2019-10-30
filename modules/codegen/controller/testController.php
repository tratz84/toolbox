<?php


use core\controller\BaseController;

class testController extends BaseController {
    
    
    public function action_index() {
        $p = new \codegen\PhpCodeParser();
        
        $p->parse(dirname(__FILE__).'/interesting.php');
        
    }
    
    
}