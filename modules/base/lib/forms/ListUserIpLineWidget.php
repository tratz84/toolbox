<?php


namespace base\forms;

use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\TextField;

class ListUserIpLineWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        parent::__construct($methodObjectList);
        
        $this->init();
    }
    
    protected function init() {
        $this->addWidget( new HiddenField('user_ip_id', '', 'Id') );
        $this->addWidget( new TextField('ip', '', 'Ip') );
    }
}

