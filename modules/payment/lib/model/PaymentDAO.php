<?php


namespace payment\model;


class PaymentDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\Payment' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from payment__payment where payment_id = ?', array($id));
	}
	
	public function delete($id) {
	    return $this->query('delete from payment__payment where payment_id = ?', array($id));
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
	    
	    $qb->leftJoin('payment__payment',        'payment_id');
	    $qb->leftJoin('payment__payment_method', 'payment_method_id');
	    $qb->leftJoin('customer__company',       'company_id',       'payment__payment');
	    $qb->leftJoin('customer__person',        'person_id',        'payment__payment');
	    
	    $qb->setGroupBy('payment__payment.payment_id');
	    
	    $qb->setOrderBy('payment__payment.payment_id desc');
	    
	    
	    $sql = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    return $this->queryCursor($sql, $params);
	}

}

