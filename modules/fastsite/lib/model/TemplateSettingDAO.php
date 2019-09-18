<?php


namespace fastsite\model;


class TemplateSettingDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\TemplateSetting' );
	}
	
	public function readActive() {
	    return $this->queryOne('select * from fastsite__template_setting where active=true');
	}
	
	
	public function readByName($name) {
	    return $this->queryOne('select * from fastsite__template_setting where template_name = ?', array($name));
	}

	
	public function allActiveToFalse() {
	    return $this->query('update fastsite__template_setting set active=false');
	}
}

