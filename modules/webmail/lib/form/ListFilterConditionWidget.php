<?php

namespace webmail\form;

use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\SelectField;
use core\forms\TextField;

class ListFilterConditionWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        $this->setName($methodObjectList);
        
        $this->methodObjectList = $methodObjectList;
        
        $this->init();
    }
    
    protected function init() {
        
        $this->addWidget(new HiddenField('filter_id'));
        $this->addWidget(new HiddenField('filter_condition_id'));
        
        $mapFilterField = array();
        $mapFilterField[''] = 'Maak uw keuze';
        $mapFilterField['subject'] = 'Onderwerp';
        $mapFilterField['from'] = 'Van';
        $mapFilterField['to'] = 'Naar';
        $this->addWidget(new SelectField('filter_field', '', $mapFilterField, 'Veld'));
        
        
        $mapFilterType = array();
        $mapFilterType[''] = 'Maak uw keuze';
        $mapFilterType['match'] = 'match';
        $mapFilterType['starts_with'] = 'starts_with';
        $mapFilterType['ends_with'] = 'ends_with';
        $mapFilterType['contains'] = 'contains';
        $mapFilterType['regexp'] = 'regexp';
        $mapFilterType['is_spam'] = 'is_spam';
        $this->addWidget(new SelectField('filter_type', '', $mapFilterType, 'Filtersoort'));
        
        $this->addWidget(new TextField('filter_pattern', '', 'Patroon'));
        
        
    }
    
    
}