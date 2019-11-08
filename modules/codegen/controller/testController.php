<?php


use core\controller\BaseController;

class testController extends BaseController {
    
//     protected $var = 567;
    
    public function action_index() {
        
        $pcp = new \codegen\parser\PhpCodeParser();
        
        $pcp->parse(__FILE__);
        $pcp->setClassVar('testController::var', '"hopsa"', 'public');
        
        print $pcp->toString();
    }
    
}
