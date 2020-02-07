<?php


namespace payment\model;


class PaymentImportDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentImport' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from payment__payment_import where payment_import_id=?', array($id));
	}
	
	public function delete($id) {
	    $this->query('delete from payment__payment_import where payment_import_id=?', array($id));
	}
	

	public function search($opts) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->selectField('payment_import_id', 'payment__payment_import');
	    $qb->selectField('description', 'payment__payment_import');
	    $qb->selectField('created', 'payment__payment_import');
	    $qb->selectFunction('count(payment__payment_import_line.payment_import_line_id)');
	    
	    $qb->setTable('payment__payment_import');
	    $qb->leftJoin('payment__payment_import_line', 'payment_import_id');
	    $qb->setGroupBy('payment__payment_import.payment_import_id');
	    $qb->setOrderBy('payment__payment_import.created desc');
	    
	    
	    $sql = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    return $this->queryCursor($sql, $params);
	}
	
}

