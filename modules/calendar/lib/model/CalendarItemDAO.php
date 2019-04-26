<?php


namespace calendar\model;


use core\db\DatabaseHandler;

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
	    
	    $sql = "select * from cal__calendar_item ";
	    
	    $where = array();
	    $params = array();
	    
	    $where[] = "calendar_id = ?";
	    $params[] = $calendarId;

	    
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
	    
// 	    print DatabaseHandler::getInstance()->getLastQuery() . "\n\n";
// var_export($r);exit;

        return $r;
	}

}

