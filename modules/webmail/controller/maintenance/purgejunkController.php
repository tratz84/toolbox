<?php



use core\controller\BaseController;
use webmail\form\PurgeJunkForm;
use webmail\mail\ImapConnection;
use webmail\service\ConnectorService;
use webmail\mail\SolrMailActions;
use webmail\solr\SolrMailQuery;

class purgejunkController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new PurgeJunkForm();
        
        return $this->render();
    }
    
    
    public function action_test() {
        
        
        
        
    }
    
    
    public function action_purge() {
        
        $connectorService = object_container_get(ConnectorService::class);
        $connector = $connectorService->readConnector( get_var('connectorId') );
        
        // check if connector exists
        if (!$connector) {
            return $this->json([
                'error' => true,
                'message' => 'Connector not found'
            ]);
        }

        if (!$connector->getJunkConnectorImapfolderId()) {
            return $this->json([
                'error' => true,
                'message' => 'No junk folder set'
            ]);
        }
        
        
        $junkImapFolder = $connectorService->readImapFolder( $connector->getJunkConnectorImapfolderId() );
        $junkFolderName = $junkImapFolder->getFolderName();
        if (!$junkFolderName) {
            return $this->json([
                'error' => true,
                'message' => 'Invalid junk folder set'
            ]);
        }
        
        
        // close session so user can continue
        session_write_close();
        
        set_time_limit(0);
        
        try {
            // solr-index + delete eml-files
            $smq = new SolrMailQuery();
            $smq->addFacetSearch('mailboxName', ':', $junkFolderName);
            
            $sma = new SolrMailActions();
            $sma->deleteSolrMailByQuery($smq);
            
            
            // purge imap-folder
            if ($connector->getConnectorType() == 'imap') {
                $ic = ImapConnection::createByConnector($connector);
                if (!$ic->connect()) {
                    return $this->json([
                        'error' => true,
                        'message' => 'Unable to connect to server'
                    ]);
                }
                
                $ic->deleteFolder($junkFolderName);
                $ic->expunge();
                
                $ic->disconnect();
            } else {
                return $this->json([
                    'error' => true,
                    'message' => 'Purge not supported for connection type'
                ]);
            }
        }
        catch (\Exception|\Error $ex) {
            return $this->json([
                'error' => true,
                'message' => $ex->getMessage()
            ]);
        }
        
        
        return $this->json([
            'success' => true,
            'message' => 'Junk folder purged'
        ]);
    }
    
}
