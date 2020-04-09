<?php



use webmail\service\ConnectorService;

function mapAllConnectors() {
    
    $connectorService = object_container_get(ConnectorService::class);
    $connectors = $connectorService->readConnectors();
    
    $map = array();
    $map[''] = t('Make your choice');
    foreach($connectors as $c) {
        $map[$c->getConnectorId()] = $c->getDescription()?$c->getDescription():$c->getConnectorId();
    }
    
    return $map;
}

