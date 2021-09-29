<?php


namespace fail2ban\model;


class IpSetting extends base\IpSettingBase {

    public function __construct($id=0) {
        parent::__construct($id);
        
        $this->setActive( true );
    }

}

