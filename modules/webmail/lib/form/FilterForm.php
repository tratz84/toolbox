<?php

namespace webmail\form;

use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\SelectField;
use core\forms\TextField;
use webmail\service\ConnectorService;
use core\forms\validator\NotEmptyValidator;

class FilterForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('filter_id');
        
        $this->addWidget(new HiddenField('filter_id'));
        
        $this->addWidget(new CheckboxField('active', '', 'Actief'));
        $this->addWidget(new TextField('filter_name', '', 'Naam'));
        
        $this->addSelectConnector();
        
        $matchMethods = array();
        $matchMethods['match_all'] = 'Voldoen aan alle condities';
        $matchMethods['match_one'] = 'Voldoen aan enkele conditie';
        $this->addWidget(new SelectField('match_method', '', $matchMethods, 'Match methode'));
        
        $this->addWidget(new ListFilterConditionWidget('conditions'));
        
        $this->addWidget(new ListFilterActionWidget('actions'));
        
        
        $this->addValidator('filter_name', new NotEmptyValidator());
    }
    
    
    public function addSelectConnector() {
        
        $connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);
        $connectors = $connectorService->readConnectors();
        
        $mapConnectors = array();
        foreach($connectors as $c) {
            $mapConnectors[$c->getConnectorId()] = $c->getDescription();
        }
        
        $this->addWidget(new SelectField('connector_id', '', $mapConnectors, 'Connector'));
    }
    
}



