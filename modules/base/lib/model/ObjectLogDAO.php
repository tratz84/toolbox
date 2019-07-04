<?php


namespace base\model;


use core\Context;
use core\db\query\QueryBuilderWhere;

class ObjectLogDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\ObjectLog' );
	}
	
	
	public static function saveChanges($objectName, $objectId, $changes) {
	    if (Context::getInstance()->isObjectLogEnabled() == false)
	        return false;
        
	    for($x=count($changes)-1; $x >= 0; $x--) {
	        $obj = new ObjectLog();
	        $obj->setObjectName($objectName);
	        $obj->setObjectId($objectId);
	        $obj->setObjectKey($changes[$x]['key']);
// 	        $obj->setObjectLabel($changes[$x]['label']);
	        $obj->setObjectAction($changes[$x]['action']);
	        $obj->setValueOld($changes[$x]['old']);
	        $obj->setValueNew($changes[$x]['new']);
	        $obj->setCreated(date('Y-m-d H:i:s'));
	        $obj->save();
	    }
	    
	}
	
	
	public function search($opts) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('object_name', '=', $opts['object_name']));
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('object_id', '=', $opts['object_id']));
	    
	    $qb->setTable('base__object_log');
	    $qb->setOrderBy('object_log_id desc');
	    
	    return $qb->queryCursor( ObjectLog::class );
	}
	
	

}

