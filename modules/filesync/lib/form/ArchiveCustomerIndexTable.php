<?php


namespace filesync\form;


use core\forms\lists\IndexTable;

class ArchiveCustomerIndexTable extends IndexTable {
    
    protected $companyId = null;
    protected $personId = null;
    
    public function __construct() {
        parent::__construct();
        
        // LibreOfficeOnline links
        $this->setOpt('lool_links', true);
        $this->setTableClass('filesync-customer-index-table');
        
        $this->setColumn('document_date',     ['fieldDescription' => t('Date'),      'fieldType' => 'date', 'sortField' => 'document_date']);
        $this->setColumn('customer_name',     ['fieldDescription' => t('Customer name'), 'fieldType' => 'text', 'sortField' => 'customer_name', 'searchable' => true]);
        $this->setColumn('path',              ['fieldDescription' => t('Filename'), 'fieldType' => 'text', 'sortField' => 'path', 'searchable' => true]);
        $this->setColumn('subject',           ['fieldDescription' => t('Subject'),  'fieldType' => 'text', 'sortField' => 'subject', 'searchable' => true]);
        $this->setColumn('public',            ['fieldDescription' => t('Public'),   'fieldType' => 'boolean', 'searchable' => true]);
        $this->setColumn('filesize_text',     ['fieldDescription' => t('File size'), 'sortField' => 'filesize']);
        $this->setColumn('actions',           ['fieldDescription' => '', 'render' => "
            function( record ) {
               var store_file_id = record['store_file_id'];

               var anchEdit = $('<a class=\"fa fa-pencil\" />');
               anchEdit.attr('href', appUrl('/?m=filesync&c=storefile&a=edit_meta&store_file_id=' + store_file_id));
               
               var anchDel  = $('<a class=\"fa fa-trash\" />');
               anchDel.attr('href', appUrl('/?m=filesync&c=storefile&a=delete&store_file_id=' + store_file_id));
               anchDel.click( handle_deleteConfirmation_event );
               anchDel.data('description', record.fullname);

               
               var container = $('<div />');
               container.append(anchEdit);
               container.append(anchDel);
               
               return container;
       }"]);
        
        
        $this->setRowClick("function(row) {
            window.open( appUrl('/?m=filesync&c=storefile&a=download&inline=1&id=' + $(row).data('record').store_file_id), '_blank' );
        }");
    }
    
    
    public function setCompanyId( $id ) { $this->setDefaultSearchOpt('companyId', $id); }
    public function setPersonId( $id ) { $this->setDefaultSearchOpt('personId', $id); }
    
    
    public function render() {
        if ($this->companyId == null && $this->personId == null) {
//             throw new InvalidStateException('No customer id set');
        }
        
        $connectorUrl = '/?m=filesync&c=archiveOverview&a=search';
        $this->setConnectorUrl( $connectorUrl );
        
        
        return parent::render();
    }
    
}

