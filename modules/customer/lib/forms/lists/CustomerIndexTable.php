<?php


namespace customer\forms\lists;


use core\forms\lists\IndexTable;

class CustomerIndexTable extends IndexTable {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->setContainerId('#person-table-container');
        $this->setConnectorUrl('/?m=customer&c=customer&a=search');
        
        $this->setRowClick("function(row, evt) {
            var type = $(row).data('record').type;
            if (type == 'company') {
                window.location = appUrl('/?m=customer&c=company&a=edit&company_id=' + $(row).data('record').id);
            }
            else if (type == 'person') {
                window.location = appUrl('/?m=customer&c=person&a=edit&person_id=' + $(row).data('record').id);
            }
        }");
        
        $this->setColumn('customer_type', [
            'width' => 100,
            'fieldDescription' => 'Type',
            'fieldType' => 'select',
            'searchable' => true,
            'filterOptions' => [
                [ 'text' => 'Type', 'value' => '' ],
                [ 'text' => t('Business customer'), 'value' => 'company' ],
                [ 'text' => t('Private'), 'value' => 'person' ],
            ],
            'render' => "function(record) {
                if (record.type == 'person') {
                    return _('Private customer');
                }
                else if (record.type == 'company') {
                    return _('Business customer');
                }
                else {
                    return record.type;
                }
            }"
        ]);
        
        $this->setColumn('name', [
            'fieldDescription' => t('Customer name'),
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
                var id = record['id'];
                
                var anchEdit = $('<a class=\"fa fa-pencil\" />');
                
                if (record.type == 'company')
                    anchEdit.attr('href', appUrl('/?m=customer&c=company&a=edit&company_id=' + id));
                if (record.type == 'person')
                    anchEdit.attr('href', appUrl('/?m=customer&c=person&a=edit&person_id=' + id));
                    
                var anchDel  = $('<a class=\"fa fa-trash\" />');
                if (record.type == 'company')
                    anchDel.attr('href', appUrl('/?m=customer&c=company&a=delete&company_id=' + id));
                if (record.type == 'person')
                    anchDel.attr('href', appUrl('/?m=customer&c=person&a=delete&person_id=' + id));
                    
                anchDel.click( handle_deleteConfirmation_event );
                anchDel.data('description', record.name);
                
                
                var container = $('<div />');
                container.append(anchEdit);
                container.append(anchDel);
                
                return container;
            }"
        ]);
        
    }
    
    
}



