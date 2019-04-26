<?php



use core\controller\BaseController;
use webmail\form\ConnectorForm;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\mail\ImapConnection;
use core\exception\ObjectNotFoundException;

class connectorController extends BaseController {
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $connectorService = $this->oc->get(ConnectorService::class);
        
        $r = $connectorService->searchConnector($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['connector_id'])?(int)$_REQUEST['connector_id']:0;
        
        $connectorService = $this->oc->get(ConnectorService::class);
        if ($id) {
            $connector = $connectorService->readConnector($id);
        } else {
            $connector = new Connector();
        }
        
        $connectorForm = $this->oc->create(ConnectorForm::class, $connector);
        $connectorForm->bind($connector);
        $connectorForm->getWidget('password')->setValue('');
        
        if (is_post()) {
            $connectorForm->bind($_REQUEST);
            
            if ($connectorForm->validate()) {
                $connectorService->saveConnector($connectorForm);
                
                redirect('/?m=webmail&c=connector');
            }
        }
        
        $this->isNew = $connector->isNew();
        $this->form = $connectorForm;
        
        $this->render();
    }
    
    
    
    public function action_delete() {
        $connectorService = $this->oc->get(ConnectorService::class);
        $connector = $connectorService->readConnector((int)get_var('connector_id'));
        
        if (!$connector) {
            throw new ObjectNotFoundException('Connector not found');
        }
        
        $connectorService->deleteConnector($connector->getConnectorId());
        
        redirect('/?m=webmail&c=connector');
    }
    
    
    
    public function action_fetch_folders() {
        $connectorService = $this->oc->get(ConnectorService::class);
        
        $password = get_var('password', '');
        
        if (!$password && get_var('connector_id')) {
            $connector = $connectorService->readConnector((int)get_var('connector_id'));
            $password = $connector->getPassword();
        }
        
        $result = array();
        
        $imap = new ImapConnection(get_var('hostname'), get_var('port'), get_var('username'), $password);
        if ($imap->connect()) {
            $result['folders'] = $imap->listFolders();
            $imap->disconnect();
            
            $result['status'] = 'ok';
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Mislukt verbinding te maken';
            
            if (count($imap->getErrors())) {
                $result['message'] = $result['message']. ': ' . implode(', ', $imap->getErrors());
            }
        }
        
        $this->json( $result );
    }
    
    
}

