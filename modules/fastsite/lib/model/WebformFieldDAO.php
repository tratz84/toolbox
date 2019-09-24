<?php


namespace fastsite\model;


class WebformFieldDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\WebformField' );
	}
	
	
	public function readByForm($webformId) {
	    return $this->queryList('select * from fastsite__webform_field where webform_id = ? order by sort', array($webformId));
	}

}

