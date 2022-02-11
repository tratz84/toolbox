<?php



use core\controller\BaseController;
use webmail\service\ConnectorService;
use webmail\mail\connector\BaseMailConnector;

class testController extends BaseController {
    
    
    public function action_index() {
        
//         die('hi');
        
        $es = object_container_get( ConnectorService::class );
        $c = $es->readConnector( 1 );
        
        $hc = BaseMailConnector::createMailConnector($c);
        
        $hc->connect();
        
//         $hc->emptyFolder( 'Junk' );
        print 'hi';
        
        
        $hc->disconnect();
        
        
        
    }
}



