<?php



use core\controller\BaseController;
use webmail\form\ConnectorForm;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\mail\ImapConnection;
use core\exception\ObjectNotFoundException;
use webmail\mail\connector\BaseMailConnector;

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
        
        /** @var ConnectorService $connectorService */
        $connectorService = $this->oc->get(ConnectorService::class);
        if ($id) {
            $connector = $connectorService->readConnector($id);
        } else {
            $connector = new Connector();
        }
        
        $connectorForm = $this->oc->create(ConnectorForm::class, $connector);
        $connectorForm->bind($connector);
        $connectorForm->getWidget('password')->setValue('');
        
        if ($connector->getPassword()) {
            $connectorForm->getWidget('password')->setPlaceholder( t('Password set') );
        } else {
            $connectorForm->getWidget('password')->setPlaceholder( t('No password set') );
        }
        
        
        if (is_post()) {
            $connectorForm->bind($_REQUEST);
            
            if ($connectorForm->validate()) {
                $connectorId = $connectorService->saveConnector($connectorForm);
                
                report_user_message(t('Changes saved'));
                redirect('/?m=webmail&c=connector&a=edit&connector_id='.$connectorId);
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
        
        $pw_db = '';
        if (get_var('connector_id')) {
            $connector = $connectorService->readConnector((int)get_var('connector_id'));
            $pw_db = $connector->getPassword();
        } else {
            $connector = new Connector();
        }
        
        $connectorForm = $this->oc->create(ConnectorForm::class, $connector);
        $connectorForm->bind($_REQUEST);
        $connectorForm->fill( $connector, array_keys($_REQUEST) );
        
        if (get_var('password')) {
            $connector->setPassword( get_var('password') );
        } else {
            $connector->setPassword( $pw_db );
        }
        
        
        $result = array();
        
        try {
            $mailcon = BaseMailConnector::createMailConnector($connector);
            
            if ($mailcon->connect()) {
                $result['folders'] = $mailcon->listFolders();
                $mailcon->disconnect();
                
                $result['status'] = 'ok';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Mislukt verbinding te maken';
                if (count($mailcon->getErrors())) {
                    $result['message'] = $result['message']. ': ' . implode(', ', $mailcon->getErrors());
                }
            }
        } catch (\Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = $ex->getMessage();
        } catch (\Error $err) {
            $result['status'] = 'error';
            $result['message'] = $err->getMessage();
        }
        
        $this->json( $result );
    }
    
    
}

