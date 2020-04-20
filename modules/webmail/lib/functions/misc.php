<?php



use webmail\service\ConnectorService;
use webmail\solr\SolrMail;

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

function mapMailActions() {
    $mapActions = array();
    $mapActions[ SolrMail::ACTION_OPEN ]      = t('Open');
    $mapActions[ SolrMail::ACTION_URGENT ]    = t('Urgent');
    $mapActions[ SolrMail::ACTION_INPROGRESS ]= t('In progress');
    $mapActions[ SolrMail::ACTION_POSTPONED ] = t('Postponed');
    $mapActions[ SolrMail::ACTION_DONE ]      = t('Done');
    $mapActions[ SolrMail::ACTION_REPLIED ]   = t('Replied');
    $mapActions[ SolrMail::ACTION_IGNORED ]   = t('Ignored');
    $mapActions[ SolrMail::ACTION_PENDING ]   = t('Pending');
    
    return $mapActions;
}



