<?php


namespace webmail\form;


use core\forms\lists\IndexTable;

class WebmailOutboxIndexTable extends IndexTable {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->setContainerId('webmail-outbox-container');
        $this->setTableClass('webmail-outbox-index-table');
        $this->setItVariable('woutbox_it');
        
        $this->setColumn('from_name',     ['fieldDescription' => t('From'),      'fieldType' => 'text']);
//         $this->setColumn('customer_name',     ['fieldDescription' => t('Customer name'), 'fieldType' => 'text', 'sortField' => 'customer_name', 'searchable' => true]);
        $this->setColumn('statusAsText',         ['fieldDescription' => t('Status'), 'fieldType' => 'text']);
        
        $this->setColumn('subject',              ['fieldDescription' => t('Subject'), 'fieldType' => 'text', 'searchable' => true]);
        $this->setColumn('created',           ['fieldDescription' => t('Created'),  'fieldType' => 'datetime']);
        
        $connectorUrl = '/?m=webmail&c=email&a=search';
        $this->setConnectorUrl( $connectorUrl );
    }
    
    
    public function setCompanyId( $id ) { $this->setDefaultSearchOpt('companyId', $id); }
    public function setPersonId( $id ) { $this->setDefaultSearchOpt('personId', $id); }
    
    
    public function render() {
        
        
        return parent::render();
    }
    
}


