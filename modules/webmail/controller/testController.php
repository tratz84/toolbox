<?php



use core\controller\BaseController;
use webmail\service\ConnectorService;
use webmail\mail\connector\BaseMailConnector;

class testController extends BaseController {
    
    
    public function action_index() {
        
        $es = object_container_get( ConnectorService::class );
        $c = $es->readConnector( 1 );
        
        $hc = BaseMailConnector::createMailConnector($c);
        
        $r = $hc->connect();
//         $folders = $hc->listFolders();
//         $hc->importItems('Duic');
$hc->poll();
        
        $hc->disconnect();
        
//         var_export($folders);
        
        
        
    }
}



