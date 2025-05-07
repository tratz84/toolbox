<?php


namespace webmail\model;


class MasterTemplateDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\MasterTemplate' );
	}
	
	
	
	public function read( $id ) {
	    $sql = "select *
                from mailing__master_template
                where master_template_id = ?";
	    
	    return $this->queryOne($sql, array($id));
	}
	
	
	
	
	public function readAll() {
	    $sql = "select master_template_id, name, sort, edited, created
                from mailing__master_template
                order by sort, name";
	    
	    return $this->queryList( $sql );
	}
	

}

