<?php


namespace invoice\model;

use core\Context;
use core\db\DatabaseHandler;


class InvoiceDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\Invoice' );
	}


	public function invoiceStatusToNull($id) {
	    $this->query("update invoice__invoice set invoice_status_id = null where invoice_status_id = ?", array($id));
	}


	public function updateStatus($invoiceId, $invoiceStatusId) {
	    $this->query('update invoice__invoice set invoice_status_id = ? where invoice_id = ?', array($invoiceStatusId, $invoiceId));
	}

	public function search($opts = array()) {
	    $where = array();
	    $params = array();

	    $sql = "select i.*, `is`.description invoice_status_description, c.company_name, p.firstname, p.insert_lastname, p.lastname
                from invoice__invoice i
                left join invoice__invoice_status `is` using (invoice_status_id)
                left join customer__company c using (company_id)
                left join customer__person p using (person_id) ";


	    if (isset($opts['invoiceNumberText']) && trim($opts['invoiceNumberText'])) {
	        $prefix = Context::getInstance()->getPrefixNumbers();

	        $nr = $opts['invoiceNumberText'];

	        if (stripos($opts['invoiceNumberText'], $prefix) === 0)
	            $nr = substr($nr, strlen($prefix));

            $where[] = " i.invoice_number LIKE ? ";
            $params[] = '%'.$nr;
	    }

	    if (isset($opts['ref_invoice_id'])) {
	        $where[] = " i.ref_invoice_id = ? ";
	        $params[] = $opts['ref_invoice_id'];
	    }
	    
	    if (isset($opts['company_id'])) {
	        $where[] = " i.company_id = ? ";
	        $params[] = $opts['company_id'];
	    }

	    if (isset($opts['person_id'])) {
	        $where[] = " i.person_id = ? ";
	        $params[] = $opts['person_id'];
	    }

	    if (isset($opts['invoice_status_description']) && is_numeric($opts['invoice_status_description'])) {
	        $where[] = " i.invoice_status_id = ? ";
	        $params[] = $opts['invoice_status_description'];
	    }
	    
	    if (isset($opts['invoiceStatusIds']) && is_array($opts['invoiceStatusIds'])) {
	        $ids = array();
	        foreach($opts['invoiceStatusIds'] as $isid) {
	            if (is_numeric($isid))
	                $ids[] = (int)$isid;
	        }
	        
	        if (count($ids)) {
	           $where[] = " i.invoice_status_id IN (" . implode(', ', $ids) . ") ";
	        }
	    }

	    if (isset($opts['customer_name']) && trim($opts['customer_name'])) {
	        $where[] = " concat(p.lastname, ', ', p.insert_lastname, ' ', p.firstname) LIKE ? OR c.company_name LIKE ? ";
	        $params[] = '%'.$opts['customer_name'].'%';
	        $params[] = '%'.$opts['customer_name'].'%';
	    }
	    if (isset($opts['subject']) && trim($opts['subject'])) {
	        $where[] = ' i.subject LIKE ? ';
	        $params[] = '%'.$opts['subject'].'%';
	    }

	    if (count($where)) {
	        $sql .= " WHERE (".implode(") AND (", $where) . ") ";
	    }

	    $sql .= "
                order by created desc, invoice_id desc";

	    return $this->queryCursor($sql, $params);
	}


	public function read($id) {
	    return $this->queryOne("select * from invoice__invoice where invoice_id = ?", array($id));
	}

	public function readByInvoiceNumber($nr) {
		return $this->queryOne('select * from invoice__invoice where invoice_number = ?', array($nr));
	}

	public function getLastInvoiceNumber() {
		$n = $this->queryValue("select max(invoice_number) from invoice__invoice");
		if (!$n || $n < 1000)
	        $n = 1000;

		return $n;
	}

	public function generateInvoiceNumber() {
		$n = $this->getLastInvoiceNumber();

        $n++;

	    return $n;
	}


	public function delete($id) {
	    $this->query("delete from invoice__invoice where invoice_id = ?", array($id));
	}



	public function readTotals($opts) {
	    $sql = "select i.company_id
                        , c.company_name
                        , i.person_id
                        , p.firstname
                        , p.insert_lastname
                        , p.lastname
                        , sum(total_calculated_price) total_billed
                        , sum(ifnull(total_calculated_price, 0)) sum_total_calculated_price
                        , sum(ifnull(total_calculated_price_incl_vat, 0)) sum_total_calculated_price_incl_vat
                        , count(*) number_invoices
                        , c.deleted company_deleted
                        , p.deleted person_deleted
                from invoice__invoice i
                left join customer__company c using (company_id)
                left join customer__person p using (person_id) ";

	    $where = array();
	    $params = array();

	    if (isset($opts['start']) && valid_date($opts['start'])) {
	        $where[] = ' invoice_date >= ? ';
	        $params[] = format_date($opts['start'], 'Y-m-d');
	    }
	    if (isset($opts['end']) && valid_date($opts['end'])) {
	        $where[] = ' invoice_date <= ? ';
	        $params[] = format_date($opts['end'], 'Y-m-d');
	    }

	    if (isset($opts['invoice_status_id']) && $opts['invoice_status_id']) {
	        $where[] = 'invoice_status_id = ?';
	        $params[] = $opts['invoice_status_id'];
	    }

	    if (count($where)) {
	        $sql .= ' where ('.implode(') AND (', $where) . ') ';
	    }

	    $sql .= "group by i.company_id, i.person_id
                order by sum(total_calculated_price) desc";

	    $res = $this->query($sql, $params);
	    $rows = array();
	    while ($r = $res->fetch_assoc()) {
	        $rows[] = $r;
	    }

	    return $rows;
	}

	public function getLastInvoiceDate() {
		return $this->queryValue('select max(invoice_date) from invoice__invoice');
	}
	
	public function getInvoiceNumberLengths() {
	    $sql = "select distinct length(invoice_number)
        	    from invoice__invoice
        	    where length(invoice_number) is not null";
	    
	    $lengths = array();
	    $ret = $this->query($sql);
	    while($row = $ret->fetch_array()) {
	        $lengths[] = $row[0];
	    }
	    
	    return $lengths;
	}

	
	public function totalsPerMonth($start, $end) {
	    $sql = "select date_format(invoice_date, '%Y-%m') as month, sum(total_calculated_price), sum(total_calculated_price_incl_vat)
                from invoice__invoice
                WHERE invoice_date >= ? AND invoice_date <= ?
                group by date_format(invoice_date, '%Y-%m')
                order by date_format(invoice_date, '%Y-%m') desc";

	    list($endYear, $endMonth) = explode('-', $end, 2);
	    $endTime = mktime(12, 0, 0, $endMonth, 12, $endYear);
	    $dtEnd = date('Y-m-t 23:59:59', $endTime);
	    
	    $q = $this->query($sql, array($start.'-01 00:00:00', $dtEnd));
	    
	    $list = array();
	    while($row = $q->fetch_array()) {
	        $list[] = array(
	            'month' => $row[0],
	            'sum_excl_vat' => $row[1],
	            'sum_incl_vat' => $row[2],
	        );
	    }
	    
	    return $list;
	    
	}
	
	
	public function sumByCustomer($companyId, $personId) {
	    $params = array();
	    $sql = "select sum(ifnull(total_calculated_price,0)) sum_total_calculated_price, sum(ifnull(total_calculated_price_incl_vat,0)) sum_total_calculated_price_incl_vat
                from invoice__invoice";
	    if ($companyId) {
	        $sql .= ' where company_id = ? ';
	        $params[] = $companyId;
	    }
	    else if ($personId) {
	        $sql .= ' where person_id = ? ';
	        $params[] = $personId;
	    }
	    else {
	        return null;
	    }
	    
	    $con = DatabaseHandler::getConnection($this->resourceName);
	    $rows = $con->queryList($sql, $params);
	    
	    return $rows[0];
	}

}
