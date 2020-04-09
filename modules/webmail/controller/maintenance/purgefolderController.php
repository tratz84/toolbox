<?php



use core\controller\BaseController;
use webmail\form\PurgeFolderForm;
use webmail\mail\ImapConnection;
use webmail\mail\SolrMailActions;
use webmail\service\ConnectorService;
use webmail\solr\SolrMailQuery;

class purgefolderController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new PurgeFolderForm();
        
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
        
        // fetch folder
        $imapFolderName = null;
        if (get_var('folderName') == 'junk') {
            $junkImapFolder = $connectorService->readImapFolder( $connector->getJunkConnectorImapfolderId() );
            if ($junkImapFolder) {
                $imapFolderName = $junkImapFolder->getFolderName();
            }
        }
        
        
        // validate folder
        if (!$imapFolderName) {
            return $this->json([
                'error' => true,
                'message' => 'Folder not found'
            ]);
        }
        
        
        // close session so user can continue
        session_write_close();
        
        set_time_limit(0);
        
        try {
            // solr-index + delete eml-files
            $smq = new SolrMailQuery();
            $smq->addFacetSearch('mailboxName', ':', $imapFolderName);
            
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
                
                $ic->deleteFolder($imapFolderName);
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
            'message' => $imapFolderName.' purged'
        ]);
    }
    
}
