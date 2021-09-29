<?php


namespace fail2ban\service;


use core\service\ServiceBase;
use fail2ban\model\IpSettingDAO;
use fail2ban\model\IpSetting;
use core\exception\ObjectNotFoundException;

class Fail2banService extends ServiceBase {
    
    
    
    public function readIpSettings() {
        $lDao = new IpSettingDAO();
        
        return $lDao->readAll();
    }
    
    public function readIpSetting( $id ) {
        $lDao = new IpSettingDAO();
        $is = $lDao->read( $id );
        
        if (!$is) {
            throw new ObjectNotFoundException('IpSetting not found');
        }
        
        return $is;
    }
    
    public function deleteIpSetting($id) {
        $lDao = new IpSettingDAO();
        $is = $lDao->delete( $id );
    }
    
    
    public function saveIpSetting( $form ) {
        
        $id = $form->getWidgetValue('ip_setting_id');
        
        if ($id) {
            $ipSetting = $this->readIpSetting( $id );
        }
        else {
            $ipSetting = new IpSetting();
        }
        
        
        
        $form->fill( $ipSetting, array('ip', 'description', 'note', 'type', 'active') );
        
        if ($ipSetting->isNew()) {
            $isDao = new IpSettingDAO();
            $ipSetting->setSort( $isDao->nextSort() );
        }
        
        $ipSetting->save();
        
        
        return $ipSetting;
    }
    
    
    public function updateSortIpSetting($ids) {
        if (is_string($ids))
            $ids = explode(',', $ids);
        
        $ids2 = array();
        foreach($ids as $i) {
            $i = (int)$i;
            if ($i) {
                $ids2[] = $i;
            }
        }
        
        
        if (count($ids2) > 0) {
            $isDao = new IpSettingDAO();
            $isDao->updateSort( $ids2 );
        }
    }
    
    
}


