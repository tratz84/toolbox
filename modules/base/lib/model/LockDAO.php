<?php


namespace base\model;


class LockDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Lock' );
	}
	
	
	public function deleteLock( $name ) {
	    $this->query('delete from base__lock where lock_name = ?', array($name));
	    return $this->getAffectedRows() > 0;
	}
	
	public function cleanupLocks() {
	    $this->query('delete from base__lock where expires is not null and expires <= ?', array(date('Y-m-d H:i:s')));
	}

}

