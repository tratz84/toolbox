<?php

namespace twofaauth\form;


use core\forms\lists\IndexTable;

class CookiesIndexTable extends IndexTable {
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->setConnectorUrl( appUrl('/?m=twofaauth&c=cookies&a=search') );
        
        $this->setColumn('secret_key', [
            'fieldType' => 'text'
            , 'fieldDescription' => t('Secret key')
        ]);
        $this->setColumn('username', [
            'fieldType' => 'text'
            , 'fieldDescription' => t('Username')
        ]);
        $this->setColumn('activated', [
            'fieldType' => 'boolean'
            , 'fieldDescription' => t('Activated')
        ]);
        $this->setColumn('last_visit', [
            'fieldType' => 'datetime'
            , 'fieldDescription' => t('Last visit')
        ]);
        $this->setColumn('created', [
            'fieldType' => 'datetime'
            , 'fieldDescription' => t('Created')
        ]);
        
        $this->setColumn('actions', [
            'fieldName' => '',
            'fieldDescription' => '',
            'fieldType' => 'actions',
            'render' => "function( record ) {
        		var id = record['cookie_id'];
            
        		var anchDel  = $('<a class=\"fa fa-trash\" />');
        		anchDel.attr('href', appUrl('/?m=twofaauth&c=cookies&a=delete&id=' + id));
        		anchDel.click( handle_deleteConfirmation_event );
        		anchDel.data( 'description', record.secret_key + ' - ' + record.username );
            
            
        		var container = $('<div />');
        		container.append(anchDel);
            
        		return container;
        	}"
        ]);
        
    }
    
    
}

