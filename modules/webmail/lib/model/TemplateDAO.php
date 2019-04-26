<?php


namespace webmail\model;


class TemplateDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\Template' );
	}

	public function readAll() {
	    return $this->queryList("select * from mailing__template order by sort, template_id");
	}
	
	public function readActive() {
	    return $this->queryList("select * from mailing__template where active = true order by sort, template_id");
	}
	
	public function read($id) {
	    $l = $this->queryList("select * from mailing__template where template_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	public function delete($id) {
	    $this->query("delete from mailing__template where template_id = ?", array($id));
	}
	
	
	public function readByCode($c) {
	    $l = $this->queryList("select * from mailing__template where template_code = ?", array($c));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}

}

