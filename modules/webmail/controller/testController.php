<?php



use core\controller\BaseController;
use webmail\service\ConnectorService;
use webmail\mail\connector\BaseMailConnector;

class testController extends BaseController {
    
    
    public function action_index() {
        
        die('hi');
        
        $es = object_container_get( ConnectorService::class );
        $c = $es->readConnector( 1 );
        
        $hc = BaseMailConnector::createMailConnector($c);
        
        $r = $hc->connect();
        
        $emldata = file_get_contents('/home/timvw/Downloads/test.eml');
        
        $hc->appendMessage('stocks', $emldata);
        
        $hc->poll();
        
        $hc->disconnect();
        
        
        
    }
}



