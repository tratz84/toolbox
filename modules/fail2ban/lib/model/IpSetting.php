<?php


namespace fail2ban\model;


class IpSetting extends base\IpSettingBase {

    public function __construct($id=0) {
        parent::__construct($id);
        
        $this->setActive( true );
    }
    
    public function match( $ip ) {
        if (trim($this->getIp()) == '') {
            return false;
        }
        
        return fnmatch( $this->getIp(), $ip, FNM_CASEFOLD|FNM_NOESCAPE);
    }

}

