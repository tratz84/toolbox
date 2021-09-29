<?php


namespace fail2ban\model;


class IpSettingDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fail2ban\\model\\IpSetting' );
	}
	
	
	public function read($id) {
	    $sql = "select *
                from fail2ban__ip_setting
                where ip_setting_id = ?";
	    
	    return $this->queryOne( $sql, array($id) );
	}

	public function delete($id) {
	    $sql = "delete
                from fail2ban__ip_setting
                where ip_setting_id = ?";
	    
	    return $this->query( $sql, array($id) );
	}
	
	
	public function readAll() {
	    $sql = "select *
                from fail2ban__ip_setting
                order by sort";
	    
	    return $this->queryList( $sql );
	}
	
	
	public function nextSort() {
	    $c = $this->queryValue('select max(sort) from fail2ban__ip_setting');
	    
	    return intval($c)+1;
	}
	
}

