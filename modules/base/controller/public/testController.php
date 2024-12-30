<?php

    
use core\controller\BaseController;
use base\service\UserService;
use core\db\DatabaseHandler;
use calendar\model\base\CalendarItemBase;
use core\container\ObjectHookProxy;
use customer\model\Person;
use customer\service\PersonService;
use core\parser\SqlQueryParser;

class testController extends BaseController {
    
    
    public function action_index() {
	    die('halt');
        
        ini_set('display_errors', 'on');
        ini_set('error_reporting', E_ALL);
        
        print 'hi';
        
        $p = new SqlQueryParser();
        $p->parseQuery("select c.*
                        from customer__company c
                        left join customer__company_address ca on (c.company_id = ca.company_id)
                        join customer__address a on (ca.address_id = a.address_id)
                        where a.city like ?");
        
        var_export( $p );exit;
        
        
    }
    
    
}
