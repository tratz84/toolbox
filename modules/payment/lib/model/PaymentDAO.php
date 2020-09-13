<?php


namespace payment\model;


use core\db\DatabaseHandler;
use core\db\query\QueryBuilderWhere;

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
	    
	    
	    if (isset($opts['company_id']) && $opts['company_id']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('payment__payment.company_id', '=', $opts['company_id']));
	    }
	    if (isset($opts['person_id']) && $opts['person_id']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('payment__payment.person_id', '=', $opts['person_id']));
	    }
	    
	    
	    $sql = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    return $this->queryCursor($sql, $params);
	}

	
	public function sumByCustomer($companyId, $personId) {
	    $params = array();
	    $sql = "select sum(ifnull(amount, 0)) sum_amount
                from payment__payment
                where cancelled = false";
	    if ($companyId) {
	        $sql .= ' and company_id = ? ';
	        $params[] = $companyId;
	    }
	    else if ($personId) {
	        $sql .= ' and person_id = ? ';
	        $params[] = $personId;
	    }
	    else {
	        return null;
	    }
	    
	    $con = DatabaseHandler::getConnection($this->resourceName);
	    $rows = $con->queryList($sql, $params);
	    
	    return $rows[0];
	}
	
	
	
	public function readTotals($opts) {
	    $sql = "select c.company_id
                    , c.company_name
                    , p.person_id
                    , p.firstname
                    , p.insert_lastname
                    , p.lastname
                    , sum(amount) total_amount
                    , count(*) number_payments
                    , c.deleted company_deleted
                    , p.deleted person_deleted
                from payment__payment
                left join customer__company c using (company_id)
                left join customer__person p using (person_id) ";
	    
	    $where = array();
	    $params = array();
	    
	    // hmz..
	    if (isset($opts['start']) && valid_date($opts['start'])) {
	        $where[] = ' payment_date >= ? ';
	        $params[] = format_date($opts['start'], 'Y-m-d');
	    }
	    if (isset($opts['end']) && valid_date($opts['end'])) {
	        $where[] = ' payment_date <= ? ';
	        $params[] = format_date($opts['end'], 'Y-m-d');
	    }
	    
	    $where[] = 'cancelled = false';
	    
	    if (count($where)) {
	        $sql .= ' where ('.implode(') AND (', $where) . ') ';
	    }
	    
	    $sql .= "group by payment__payment.company_id, payment__payment.person_id";
	    
	    $res = $this->query($sql, $params);
	    $rows = array();
	    while ($r = $res->fetch_assoc()) {
	        $rows[] = $r;
	    }
	    
	    return $rows;
	}
	
}

