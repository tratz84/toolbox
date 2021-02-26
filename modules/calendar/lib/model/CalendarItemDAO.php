<?php


namespace calendar\model;



class CalendarItemDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\calendar\\model\\CalendarItem' );
	}
	
	
	public function read($id) {
	    $l = $this->queryList("select * from cal__calendar_item where calendar_item_id = ?", array($id));
	    
	    if (count($l))
	        return $l[0];
	    else
	        return null;
	}
	
	public function delete($id) {
	    $this->query("delete from cal__calendar_item where calendar_item_id = ?", array($id));
	}
	
	public function updateExDate($calendarItemId, $exdate) {
	    $sql = "update cal__calendar_item set exdate = ? where calendar_item_id = ?";
	    
	    $this->query($sql, array($exdate, $calendarItemId));
	}
	
	
	public function readByDate($calendarId, $start, $end) {
	    return $this->readByOpts(['calendar_id' => $calendarId], $start, $end);
	}
	
	public function readByOpts($opts, $start, $end) {
	    
	    $sql = "select cal__calendar_item.*, customer__company.company_name, customer__person.firstname, customer__person.insert_lastname, customer__person.lastname ";
	    $sql .= " from cal__calendar_item ";
	    
	    $sql .= ' left join customer__company on (cal__calendar_item.company_id = customer__company.company_id) ';
	    $sql .= ' left join customer__person on (cal__calendar_item.person_id = customer__person.person_id) ';
	    
	    
	    $where = array();
	    $params = array();
	    
	    if (isset($opts['calendar_id'])) {
    	    $where[] = "calendar_id = ?";
    	    $params[] = $opts['calendar_id'];
	    }
	    if (isset($opts['company_id'])) {
	        $where[] = "cal__calendar_item.company_id = ?";
	        $params[] = $opts['company_id'];
	    }
	    else if (isset($opts['person_id'])) {
	        $where[] = "cal__calendar_item.person_id = ?";
	        $params[] = $opts['person_id'];
	    }
	    
	    if (count($where) == 0) {
	        // TODO: throw exception?
	    }

	    
	    $sql_dates = '';
	    $sql_dates .= " (start_date >= ? and start_date <= ?) ";
	    $params[] = $start;
	    $params[] = $end;
	    
	    $sql_dates .= ' OR (start_date <= ? and (end_date is null OR end_date >= ?) and recurrence_rule is not null) ';
	    $params[] = $start;
	    $params[] = $start;

	    $sql_dates .= ' OR (start_date >= ? and (end_date is null OR end_date <= ?) and recurrence_rule is not null) ';
	    $params[] = $start;
	    $params[] = $end;
	    
	    
	    $where[] = $sql_dates;
	    
	    
	    if (count($where)) {
	        $sql .= " where (".implode(") AND (", $where) . ")";
	    }
	    
	    $r = $this->queryList($sql, $params);
	    
	    for($x=0; $x < count($r); $x++) {
    	    $r[$x]->setField('customer_name', format_customername($r[$x]));
	    }
	    
// 	    print DatabaseHandler::getInstance()->getLastQuery() . "\n\n";
// var_export($r);exit;

        return $r;
	}
	
	
	public function getFirstCalendarDate($opts) {
	    $sql = "select min(start_date) from cal__calendar_item ";
	    
	    if (isset($opts['calendar_id'])) {
	        $where[] = "calendar_id = ?";
	        $params[] = $opts['calendar_id'];
	    }
	    if (isset($opts['company_id'])) {
	        $where[] = "cal__calendar_item.company_id = ?";
	        $params[] = $opts['company_id'];
	    }
	    else if (isset($opts['person_id'])) {
	        $where[] = "cal__calendar_item.person_id = ?";
	        $params[] = $opts['person_id'];
	    }
	    
	    if (count($where) == 0) {
	        // TODO: throw exception?
	    }
	    
	    if (count($where)) {
	        $sql .= " where (".implode(") AND (", $where) . ")";
	    }
	    
	    return $this->queryValue($sql, $params);
	}
	
}


