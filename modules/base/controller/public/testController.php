<?php

    
use core\controller\BaseController;
use base\service\UserService;
use core\db\DatabaseHandler;
use calendar\model\base\CalendarItemBase;
use core\container\ObjectHookProxy;
use customer\model\Person;
use customer\service\PersonService;

class testController extends BaseController {
    
    
    public function action_index() {
        
        ini_set('display_errors', 'on');
        ini_set('error_reporting', E_ALL);
        
        $p = object_container_get( PersonService::class );
        
        print toolbox_get_class($p);
        
        
    }
    
    
}