<?php


namespace fail2ban\form;


use core\forms\lists\IndexTable;

class IpSettingIndexTable extends IndexTable {
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->setContainerId('is-container');
        $this->autoSetItVariable('it_is');
        
        
        $this->setColumn('sort', [
            'fieldDescription' => ''
            , 'render' => "function() {
                var handle = $('<span class=\"fa fa-sort cursor\"></span>');
                return handle;
            }"
        ]);
        
        
        $this->setColumn('ip', [
            'fieldDescription' => 'IP'
        ]);
        $this->setColumn('description', [
            'fieldDescription' => t('Description')
        ]);
        $this->setColumn('type', [
            'fieldDescription' => t('Type')
        ]);
        $this->setColumn('active', [
            'fieldDescription' => t('Active')
            , 'fieldType' => 'boolean'
        ]);
        
        $this->setRowClick("function(row, evt) {
            var rec = $(row).data('record');
            
            // don't trigger when sort-col is clicked
            if ($(evt.target).hasClass('.td-sort') || $(evt.target).closest('.td-sort').length > 0) return;

            window.location = appUrl('/?m=fail2ban&c=ipsettings&a=edit&id=' + rec.ip_setting_id);
        }");
        
        $this->setConnectorUrl( appUrl('/?m=fail2ban&c=ipsettings&a=search') );
    }
    
    
}

