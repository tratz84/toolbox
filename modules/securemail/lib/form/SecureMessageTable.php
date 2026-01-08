<?php

namespace securemail\form;


use core\forms\lists\IndexTable;

class SecureMessageTable extends IndexTable {
    public function __construct() {
        parent::__construct();
        
        $this->setContainerId('object-container-crit');
        $this->setOption( 'searchContainer', '.form-secure-message-search-form' );
        $this->setConnectorUrl( appUrl('/?m=securemail&c=secureMessage&a=search') );
        
        $this->setColumn('secure_message_id', [
            'width' => 40
            , 'fieldDescription' => 'Id'
            , 'fieldType' => 'text'
        ]);
        $this->setColumn('name', [
            'fieldDescription' => 'Klant'
            , 'fieldType' => 'text'
            , 'render' => "function(record) {
                return format_customername(record);
            }"
        ]);
        $this->setColumn('short_description', [
            'fieldDescription' => 'Korte omschrijving'
            , 'fieldType' => 'text'
        ]);
        
        $this->setColumn('ttl_type', [
            'fieldDescription' => 'TTL'
            , 'fieldType' => 'text'
            , 'render' => "function(rec) {
                return t('securemessage-ttl.'+rec.ttl_type);
            }"
        ]);

        $this->setColumn('ttl_expires_on', [
            'fieldDescription' => 'Expires'
            , 'fieldType' => 'datetime'
            , 'render' => "function(rec) {
                if (rec.ttl_type != 'expires') return '';

                return format_datetime_minuts( str2date(rec.ttl_expires_on, { dmy: true }) );
            }"
        ]);
        
        $this->setColumn('deleted', [
            'fieldDescription' => t('Deleted')
            , 'fieldType' => 'datetime'
        ]);
        
        $this->setColumn('created', [
            'fieldDescription' => t('Created')
            , 'fieldType' => 'datetime'
        ]);
        
        
        $this->setRowClick("function(row, evt) {
                                var record = $(row).data('record');
            
                                window.location = appUrl('/?m=securemail&c=secureMessage&a=edit&sm_id=' + record.secure_message_id);
                            }");
    }
}


