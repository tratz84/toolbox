<?php

namespace webmail\form;

use core\ObjectContainer;
use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\SelectField;
use webmail\service\ConnectorService;
use core\forms\WidgetContainer;

class ListFilterActionWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        parent::__construct($methodObjectList);
        
        $this->init();
    }
    
    protected function init() {
        
        $this->addWidget(new HiddenField('filter_id'));
        $this->addWidget(new HiddenField('filter_action_id'));
        
        
        $mapFilterAction = array();
        $mapFilterAction['move_to_folder'] = 'move_to_folder';
        $mapFilterAction['set_action']     = 'set_action';
        $this->addWidget(new SelectField('filter_action', '', $mapFilterAction, 'Actie'));
        
        $mapFilterActionProperty = array();
//         $mapFilterActionProperty[''] = 'Maak uw keuze';
        $mapFilterActionProperty['name'] = 'name';
        $this->addWidget(new SelectField('filter_action_property', '', $mapFilterActionProperty, 'Type'));
        
        
        $wc = new WidgetContainer();
        
        $connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);
        $folders = $connectorService->readImapFolders();
        $mapFolders = array();
        $mapFolders[''] = 'Maak uw keuze';
        foreach($folders as $f) {
            $mapFolders[$f->getConnectorImapfolderId()] = $f->getFolderName();
        }
        $wc->addWidget(new SelectField('move_to_folder_filter_action_value', '', $mapFolders, 'Waarde'));
        
        
        $mapActions = mapMailActions();
        $wc->addWidget( new SelectField('set_action_filter_action_value', '', $mapActions, 'Waarde') );
        
        $this->addWidget( $wc );
        
        
    }
    
    
}