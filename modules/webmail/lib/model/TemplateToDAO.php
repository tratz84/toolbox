<?php


namespace webmail\model;


class TemplateToDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\TemplateTo' );
	}
	
	
	public function readByTemplate($templateId) {
	    return $this->queryList('select * from mailing__template_to where template_id = ? order by sort', array($templateId));
	}
	
	public function deleteByTemplate($templateId) {
	    $this->query('delete from mailing__template_to where template_id = ? order by sort', array($templateId));
	}
	

}

