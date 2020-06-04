<?php


namespace codegen\form\widgetoptions;

use core\forms\SelectField;
use core\forms\RadioField;


class ListEditWidgetsOptionsForm extends DefaultWidgetOptionsForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->removeWidget('label');
        $this->removeWidget('defaultValue');
        
        $this->addWidget(new RadioField('relationType', '', ['MTON' => 'MTON', 'MTO1' => 'MTO1'], 'Relation type'));
        $this->addWidget(new SelectField('linkDaoClass', '', codegen_map_dao_classes(), 'Link DAO'));
        
    }
    
}