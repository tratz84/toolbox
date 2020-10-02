<?php


namespace customer\forms\lists;

use core\forms\lists\IndexTable;

class CompanyIndexTable extends IndexTable {
    
    public function __construct() {
        parent::__construct();
        
        
        
        
        $this->setContainerId('#company-table-container');
        $this->setConnectorUrl('/?m=customer&c=company&a=search');
        
        $this->setRowClick("function(row, evt) {
                            	window.location = appUrl('/?m=customer&c=company&a=edit&company_id=' + $(row).data('record').company_id);
                            }");
        
        $this->setColumn('company_name', [
            'fieldDescription' => t('Company name'),
            'fieldType' => 'text',
            'searchable' => true
        ]);
        
        $this->setColumn('contact_person', [
            'fieldName' => 'contact_person',
            'fieldDescription' => t('Contact person'),
            'fieldType' => 'text',
            'searchable' => true
        ]);
        
        $this->setColumn('actions', [
            'fieldName' => '',
            'fieldDescription' => '',
            'fieldType' => 'actions',
            'render' => "function( record ) {
                    		var company_id = record['company_id'];
                    		
                    		var anchEdit = $('<a class=\"fa fa-pencil\" />');
                    		anchEdit.attr('href', appUrl('/?m=customer&c=company&a=edit&company_id=' + company_id));
                    		
                    		var anchDel  = $('<a class=\"fa fa-trash\" />');
                    		anchDel.attr('href', appUrl('/?m=customer&c=company&a=delete&company_id=' + company_id));
                    		anchDel.click( handle_deleteConfirmation_event );
                    		anchDel.data('description', record.company_name);
                    
                    		
                    		var container = $('<div />');
                    		container.append(anchEdit);
                    		container.append(anchDel);
                    		
                    		return container;
                    	}"
        ]);
        
        
    }
    
}

