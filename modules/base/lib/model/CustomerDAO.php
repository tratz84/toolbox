<?php


namespace base\model;



class CustomerDAO extends \core\db\DAOObject {
    
    public function __construct() {
        $this->setResource( 'default' );
        $this->setObjectName( '\\base\\model\\Customer' );
    }
    
    
    public function search($opts=array()) {
        $params = array();
 
        if ($opts['companiesEnabled']) {
            $sql1 = "select company_id id, 'company' as type, company_name as name, c.coc_number, c.vat_number, iban, bic, edited, created
                    from customer__company c ";
            
            $where1 = array();
            $where1[] = " c.deleted = false ";
            if (isset($opts['name']) && trim($opts['name'])) {
                $where1[] = ' c.company_name like ? ';
                $params[] = '%'.$opts['name'].'%';
            }
        }
        
        $where2 = array();
        if ($opts['personsEnabled']) {
            $where2[] = " customer__person.deleted = false ";
            
            $sql2 = "select person_id id, 'person' as type, concat(lastname, ', ', insert_lastname, ' ', firstname) as name, '' as coc_number, '' as vat_number, iban, bic, edited, created
                    from customer__person ";
            if (isset($opts['name']) && trim($opts['name'])) {
                $where2[] = ' concat(lastname, \', \', insert_lastname, \' \', firstname) like ? ';
                $params[] = '%'.$opts['name'].'%';
            }
            
            if (count($where2)) {
                $sql2 .= " WHERE (" . implode(") AND (", $where2) . " ) ";
            }
        }
        
        if (isset($where1) && count($where1)) {
            $sql1 .= " WHERE (" . implode(") AND (", $where1) . " ) ";
        }
        
        $sql = '';
        if (isset($sql1))
            $sql = $sql1;
        
        if ($opts['personsEnabled']) {
            if ($sql)
                $sql .= ' union ';
            
            $sql .= $sql2;
        }
        
        
        $sql .= ' order by name ';
//         print $sql;exit;
        
        return $this->queryCursor($sql, $params);
    }
    
}

