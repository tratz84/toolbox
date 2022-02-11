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
        
        $r = $hc->connect();
        
        $client = $hc->getClient();
        $q = new \Horde_Imap_Client_Search_Query();
        $q->headerText('subject', 'Account aangemaakt WebApp Bevazet Bedrijfskleding B.V.');
        
        $opts = array();
        $opts['sort'] = array(\Horde_Imap_Client::SORT_ARRIVAL);//, \Horde_Imap_Client::SORT_REVERSE);
        $uids = $client->search( 'stocks', $q, $opts );
        
        $ids = $uids['match']->ids;
        
        if (count($ids))
            $hc->deleteMailByUid('stocks', $ids[0]);
        
//         $emldata = file_get_contents('/home/timvw/Downloads/test.eml');
//         $hc->appendMessage('stocks', $emldata);
        
//         $hc->poll();
        
        $hc->disconnect();
        
        
        
    }
}



