<?php


namespace payment\model;


use core\db\query\QueryBuilderWhere;

class PaymentLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentLine' );
	}
	
	public function deleteByPayment($paymentId) {
	    $sql = "delete from payment__payment_line where payment_id = ?";
	    
	    return $this->query($sql, array($paymentId));
	}
	
	public function readByPayment($paymentId) {
	    $sql = "select * from payment__payment_line where payment_id = ? order by sort";
	    
	    return $this->queryList($sql, array($paymentId));
	}
	
	public function readExploded($paymentId) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->setTable('payment__payment_line');
	    
	    // payment__payment-fields
	    $qb->selectField('payment_id',  'payment__payment');
	    $qb->selectField('company_id',  'payment__payment');
	    $qb->selectField('person_id',   'payment__payment');
	    $qb->selectField('description', 'payment__payment', 'payment_description');
	    $qb->selectField('note',        'payment__payment', 'payment_note');
	    $qb->selectField('amount',      'payment__payment', 'payment_amount');
	    $qb->selectField('payment_date');
	    $qb->selectField('cancelled',   'payment__payment');
	    $qb->selectField('created',     'payment__payment');
	    
	    $qb->selectField('payment_line_id');
	    $qb->selectField('payment_method_id',    'payment__payment_line');
	    $qb->selectField('amount',               'payment__payment_line', 'payment_line_amount');
	    $qb->selectField('bankaccountno',        'payment__payment_line');
	    $qb->selectField('bankaccountno_contra', 'payment__payment_line');
	    $qb->selectField('code',                 'payment__payment_line', 'payment_line_code');
	    $qb->selectField('name',                 'payment__payment_line', 'payment_line_name');
	    $qb->selectField('description1',         'payment__payment_line', 'payment_line_description1');
	    $qb->selectField('description2',         'payment__payment_line', 'payment_line_description2');
	    $qb->selectField('mutation_type',        'payment__payment_line', 'payment_line_mutation_type');
	    $qb->selectField('sort',                 'payment__payment_line', 'payment_line_sort');
	    
	    $qb->selectField('code',        'payment__payment_method', 'payment_method_code');
	    $qb->selectField('description', 'payment__payment_method', 'payment_method_description');
	    $qb->selectField('active',      'payment__payment_method', 'payment_method_active');
	    $qb->selectField('deleted',     'payment__payment_method', 'payment_method_deleted');
	    
	    $qb->selectField('company_name', 'customer__company', 'company_name');
	    
	    $qb->selectField('firstname',       'customer__person', 'firstname');
	    $qb->selectField('insert_lastname', 'customer__person', 'insert_lastname');
	    $qb->selectField('lastname',        'customer__person', 'lastname');
	    
	    
	    $qb->leftJoin('payment__payment',        'payment_id');
	    $qb->leftJoin('payment__payment_method', 'payment_method_id');
	    $qb->leftJoin('customer__company',       'company_id',       'payment__payment');
	    $qb->leftJoin('customer__person',        'person_id',        'payment__payment');
	    
	    
	    $qb->setOrderBy('payment__payment.payment_id desc, payment__payment_line.sort asc');
	    
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('payment__payment.payment_id', '=', $paymentId));
	    
	    $sql = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    return $this->queryList($sql, $params);
	}
	
	
	
	public function search($opts=array()) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->setTable('payment__payment_line');
	    
	    // payment__payment-fields
	    $qb->selectField('payment_id',  'payment__payment');
	    $qb->selectField('company_id',  'payment__payment');
	    $qb->selectField('person_id',   'payment__payment');
	    $qb->selectField('description', 'payment__payment', 'payment_description');
	    $qb->selectField('note',        'payment__payment', 'payment_note');
	    $qb->selectField('amount',      'payment__payment', 'payment_amount');
	    $qb->selectField('payment_date');
	    $qb->selectField('cancelled',   'payment__payment');
	    $qb->selectField('created',     'payment__payment');
	    
	    $qb->selectField('payment_line_id');
	    $qb->selectField('payment_method_id',    'payment__payment_line');
	    $qb->selectField('amount',               'payment__payment_line', 'payment_line_amount');
	    $qb->selectField('bankaccountno',        'payment__payment_line');
	    $qb->selectField('bankaccountno_contra', 'payment__payment_line');
	    $qb->selectField('code',                 'payment__payment_line', 'payment_line_code');
	    $qb->selectField('name',                 'payment__payment_line', 'payment_line_name');
	    $qb->selectField('description1',         'payment__payment_line', 'payment_line_description1');
	    $qb->selectField('description2',         'payment__payment_line', 'payment_line_description2');
	    $qb->selectField('mutation_type',        'payment__payment_line', 'payment_line_mutation_type');
	    $qb->selectField('sort',                 'payment__payment_line', 'payment_line_sort');
	    
	    $qb->selectField('code',        'payment__payment_method', 'payment_method_code');
	    $qb->selectField('description', 'payment__payment_method', 'payment_method_description');
	    $qb->selectField('active',      'payment__payment_method', 'payment_method_active');
	    $qb->selectField('deleted',     'payment__payment_method', 'payment_method_deleted');
	    
	    $qb->selectField('company_name', 'customer__company', 'company_name');
	    
	    $qb->selectField('firstname',       'customer__person', 'firstname');
	    $qb->selectField('insert_lastname', 'customer__person', 'insert_lastname');
	    $qb->selectField('lastname',        'customer__person', 'lastname');
	    
	    
	    $qb->leftJoin('payment__payment',        'payment_id');
	    $qb->leftJoin('payment__payment_method', 'payment_method_id');
	    $qb->leftJoin('customer__company',       'company_id',       'payment__payment');
	    $qb->leftJoin('customer__person',        'person_id',        'payment__payment');
	    
	    
	    $qb->setOrderBy('payment__payment.payment_id desc, payment__payment_line.sort asc');
	    
	    
	    $sql = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    return $this->queryCursor($sql, $params);
	}

}

