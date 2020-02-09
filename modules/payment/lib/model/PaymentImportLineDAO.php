<?php


namespace payment\model;


use core\db\query\QueryBuilderWhere;

class PaymentImportLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentImportLine' );
	}
	
	
	public function readByImport($paymentImportId) {
	    
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('payment__payment_import_line');
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('payment_import_id', '=', $paymentImportId));
	    
	    $sql = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    return $this->queryList($sql, $params);
	}
	
	public function deleteByImport($paymentImportId) {
	    $this->query('delete from payment__payment_import_line where payment_import_id=?', array($paymentImportId));
	}

}

