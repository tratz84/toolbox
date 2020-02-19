<?php


namespace payment\model;


use core\db\query\QueryBuilderWhere;

class PaymentImportLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentImportLine' );
	}
	
	
	public function read($id) {
	    $qb = $this->createQueryBuilder();
	    $qb->selectField('*', 'payment__payment_import_line');
	    $qb->selectField('company_name', 'customer__company');
	    
	    $qb->selectField('firstname', 'customer__person');
	    $qb->selectField('insert_lastname', 'customer__person');
	    $qb->selectField('lastname', 'customer__person');
	    
	    $qb->selectField('invoice_number', 'invoice__invoice');
	    
	    $qb->setTable('payment__payment_import_line');
	    $qb->leftJoin('customer__company', 'company_id');
	    $qb->leftJoin('customer__person', 'person_id');
	    $qb->leftJoin('invoice__invoice', 'invoice_id');
	    
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('payment_import_line_id', '=', $id));
	    
	    $sql = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    return $this->queryOne($sql, $params);
	}
	
	public function readByImport($paymentImportId) {
	    
	    $qb = $this->createQueryBuilder();
	    $qb->selectField('*', 'payment__payment_import_line');
	    $qb->selectField('company_name', 'customer__company');
	    
	    $qb->selectField('firstname', 'customer__person');
	    $qb->selectField('insert_lastname', 'customer__person');
	    $qb->selectField('lastname', 'customer__person');
	    
	    $qb->selectField('invoice_number', 'invoice__invoice');
	    
	    $qb->setTable('payment__payment_import_line');
	    $qb->leftJoin('customer__company', 'company_id');
	    $qb->leftJoin('customer__person', 'person_id');
	    $qb->leftJoin('invoice__invoice', 'invoice_id');
	    
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('payment_import_id', '=', $paymentImportId));
	    
	    $qb->setLimit(1000);
	    
	    $sql = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    return $this->queryList($sql, $params);
	}
	
	public function deleteByImport($paymentImportId) {
	    $this->query('delete from payment__payment_import_line where payment_import_id=?', array($paymentImportId));
	}

}

