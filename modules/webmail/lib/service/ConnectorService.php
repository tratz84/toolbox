<?php


namespace webmail\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use webmail\form\ConnectorForm;
use webmail\model\Connector;
use webmail\model\ConnectorDAO;
use webmail\model\ConnectorImapfolderDAO;
use webmail\model\FilterDAO;
use webmail\model\FilterConditionDAO;
use webmail\model\FilterActionDAO;
use webmail\form\FilterForm;
use webmail\model\Filter;

class ConnectorService extends ServiceBase {
    

    public function readActive() {
        $cDao = new ConnectorDAO();
        $connectors = $cDao->readActive();
        
//         $r = array();
//         foreach($connectors as $c) {
//             $r[] = $this->readConnector( $c->getConnectorId() );
//         }
        
        return $connectors;
    }
    
    public function readConnector($connectorId) {
        $cDao = new ConnectorDAO();
        
        $c = $cDao->read($connectorId);
        if (!$c)
            return null;
        
        // read imapfolders
        $ciDao = new ConnectorImapfolderDAO();
        $imapfolders = $ciDao->readByConnector($c->getConnectorId());
        
        usort($imapfolders, function($if1, $if2) {
            
            if ($if1->getFoldername() == 'INBOX') {
                return -1;
            }
            if ($if2->getFoldername() == 'INBOX') {
                return 1;
            }
            
            return strcmp($if1->getFoldername(), $if2->getFoldername());
        });
        $c->setImapfolders( $imapfolders );
        
        
        // read filters
        $fDao = new FilterDAO();
        $tmpFilters = $fDao->readByConnector($connectorId);
        $filters = array();
        foreach($tmpFilters as $f) {
            $filters[] = $this->readFilter($f->getFilterId());
        }
        $c->setFilters( $filters );
        
        
        
        return $c;
    }
    
    
    
    public function deleteConnector($connectorId) {
        
        $ciDao = new ConnectorImapfolderDAO();
        $ciDao->deleteByConnector($connectorId);
        
        $cDao = new ConnectorDAO();
        $cDao->delete($connectorId);
        
    }
    
    
    
    public function saveConnector(ConnectorForm $form) {
        
        $connectorId = $form->getWidgetValue('connector_id');
        if ($connectorId) {
            $connector = $this->readConnector($connectorId);
        } else {
            $connector = new Connector();
        }
        
        $isNew = $connector->isNew();
        
        $changes = $form->changes($connector);
        
        
        $form->fill($connector, array('connector_id', 'description', 'connector_type', 'hostname', 'port', 'username', 'sent_connector_imapfolder_id', 'junk_connector_imapfolder_id', 'trash_connector_imapfolder_id', 'active'));
        
        if ($form->getWidgetValue('password')) {
            $connector->setPassword($form->getWidgetValue('password'));
        }
        
        if (!$connector->save()) {
            // exception would also be on it's place
            return false;
        }
        
        $arrImapfolders = $form->getWidgetValue('imapfolders');
        $arrSelectedImapfolders = $form->getWidgetValue('selectedImapfolders');
        
        $imapfoldersInDb = $connector->getImapfolders();
        
        $imapfolders = array();
        if (is_array($arrImapfolders)) {
            for($x=0; $x < count($arrImapfolders); $x++) {
                $i = $arrImapfolders[$x];
                $imapfolders[] = array(
                    'connector_imapfolder_id' => isset($imapfoldersInDb[$x]) ? $imapfoldersInDb[$x]->getConnectorImapfolderId() : null,
                    'folderName' => $i,
                    'active' => is_array($arrSelectedImapfolders) && in_array($i, $arrSelectedImapfolders) ? '1' : '0'
                );
            }
        } else {
            for($x=0; $x < count($imapfoldersInDb); $x++) {
                $if = $imapfoldersInDb[$x];
                
                $w = $form->getWidget('selectedImapfolder-'.slugify($if->getFoldername()));
                
                $imapfolders[] = array(
                    'connector_imapfolder_id' => $if->getConnectorImapfolderId(),
                    'folderName' => $if->getFoldername(),
                    'active' => $w && $w->getValue( )? '1' : '0'
                );
            }
        }
        
        $cifDao = new ConnectorImapfolderDAO();
        $cifDao->mergeFormListMTO1('connector_id', $connector->getConnectorId(), $imapfolders);
    }
    
    
    public function searchConnector($start, $limit, $opts=array()) {
        $cDao = new ConnectorDAO();
        
        $cursor = $cDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('connector_id', 'description', 'hostname', 'username', 'active'));
        
        return $r;
    }
    
    public function readConnectors() {
        $cDao = new ConnectorDAO();
        
        return $cDao->readAll();
    }
    
    public function hasConnectors() {
        $cDao = new ConnectorDAO();
        
        return $cDao->connectorCount() > 0 ? true : false;
    }
    
    
    public function readImapFolder($connectorImapFolderId) {
        $cifDao = new ConnectorImapfolderDAO();
        
        return $cifDao->read($connectorImapFolderId);
    }
    
    public function readImapFolders() {
        $cifDao = new ConnectorImapfolderDAO();
        
        return $cifDao->readAll();
    }
    
    public function readFilter($filterId) {
        $fDao = new FilterDAO();
        $f = $fDao->read($filterId);
        
        $fcDao = new FilterConditionDAO();
        $conditions = $fcDao->readByFilter($filterId);
        
        $faDao = new FilterActionDAO();
        $actions = $faDao->readByFilter($filterId);
        
        $f->setConditions($conditions);
        $f->setActions($actions);
        
        return $f;
    }
    
    public function readFilters() {
        $fDao = new FilterDAO();
        
        $l = $fDao->readAll();
        
        $objs = array();
        foreach($l as $f) {
            $objs[] = array(
                'filter_id' => $f->getFilterId(),
                'name'      => $f->getFilterName(),
                'active'    => $f->getActive(),
                'edited'    => $f->getEdited(),
                'created'   => $f->getCreated()
            );
        }
        
        $lr = new ListResponse(0, count($l), count($l), $objs);
        
        return $lr;
    }
    
    public function saveFilter(FilterForm $form) {
        $filterId = $form->getWidgetValue('filter_id');
        if ($filterId) {
            $filter = $this->readFilter($filterId);
        } else {
            $filter = new Filter();
        }
        
        $isNew = $filter->isNew();
        
        $changes = $form->changes($filter);
        
        
        $form->fill($filter, array('filter_id', 'active', 'filter_name', 'connector_id', 'match_method'));
        
        if ($filter->isNew()) {
            $fDao = new FilterDAO();
            $filter->setSort($fDao->nextSort());
        }
        
        if (!$filter->save()) {
            // exception would also be on it's place
            return false;
        }
        
        $form->getWidget('filter_id')->setValue($filter->getFilterId());
        
        $fcDao = new FilterConditionDAO();
        $conditions = $form->getWidget('conditions')->getObjects();
        $fcDao->mergeFormListMTO1('filter_id', $filter->getFilterId(), $conditions);
        
        $faDao = new FilterActionDAO();
        $actions = $form->getWidget('actions')->getObjects();
        $faDao->mergeFormListMTO1('filter_id', $filter->getFilterId(), $actions);
        
    }
    
    
    public function updateFilterSort($filterIds) {
        for($x=0; $x < count($filterIds); $x++) {
            $filterIds[$x] = (int)$filterIds[$x];
        }
        
        $fDao = new FilterDAO();
        $fDao->updateSort($filterIds);
    }

    public function deleteFilter($filterId) {

        $faDao = new FilterActionDAO();
        $fcDao = new FilterConditionDAO();

        $faDao->deleteByFilter($filterId);
        $fcDao->deleteByFilter($filterId);

        $fDao = new FilterDAO();
        $fDao->delete($filterId);

    }
    
    
    
    
}

