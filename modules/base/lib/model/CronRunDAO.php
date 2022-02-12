<?php


namespace base\model;


class CronRunDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\CronRun' );
	}
	
	
	public function readLast($cronId, $no) {
	    $no = intval($no);
	    if ($no < 0)
	        $no = 50;
	    
	    return $this->queryList('select * 
                                from base__cron_run 
                                where cron_id = ? 
                                order by created desc
                                limit ' . $no
	        , array($cronId));
	    
	}

}

