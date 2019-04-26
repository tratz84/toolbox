<?php

namespace webmail\form;

use core\ObjectContainer;
use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\SelectField;
use webmail\service\ConnectorService;

class ListFilterActionWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        $this->setName($methodObjectList);
        
        $this->methodObjectList = $methodObjectList;
        
        $this->init();
    }
    
    protected function init() {
        
        $this->addWidget(new HiddenField('filter_id'));
        $this->addWidget(new HiddenField('filter_action_id'));
        
        
        $mapFilterAction = array();
//         $mapFilterAction[''] = 'Maak uw keuze';
        $mapFilterAction['move_to_folder'] = 'move_to_folder';
        $this->addWidget(new SelectField('filter_action', '', $mapFilterAction, 'Actie'));
        
        $mapFilterActionProperty = array();
//         $mapFilterActionProperty[''] = 'Maak uw keuze';
        $mapFilterActionProperty['name'] = 'name';
        $this->addWidget(new SelectField('filter_action_property', '', $mapFilterActionProperty, 'Type'));
        
        $connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);
        $folders = $connectorService->readImapFolders();
        $mapFolders = array();
        $mapFolders[''] = 'Maak uw keuze';
        foreach($folders as $f) {
            $mapFolders[$f->getConnectorImapfolderId()] = $f->getFolderName();
        }
        $this->addWidget(new SelectField('filter_action_value', '', $mapFolders, 'Waarde'));
        
    }
    
    
}