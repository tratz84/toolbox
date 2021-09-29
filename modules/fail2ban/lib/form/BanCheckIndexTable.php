<?php


namespace fail2ban\form;


use core\forms\lists\IndexTable;

class BanCheckIndexTable extends IndexTable {
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->setContainerId('bc-container');
        $this->autoSetItVariable('it_bc');
        
        
        $this->setColumn('ban_check_id', [
            'fieldDescription' => 'Id'
        ]);
        $this->setColumn('ip', [
            'fieldDescription' => t('IP')
        ]);
        $this->setColumn('message', [
            'fieldDescription' => t('Message')
        ]);
        $this->setColumn('created', [
            'fieldDescription' => t('Created')
            , 'fieldType' => 'datetime'
        ]);
        
        $this->setConnectorUrl( appUrl('/?m=fail2ban&c=check&a=search') );
    }
    
    
}

