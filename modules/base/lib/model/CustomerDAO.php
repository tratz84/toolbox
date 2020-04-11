<?php


namespace base\model;



class CustomerDAO extends \core\db\DAOObject {
    
    public function __construct() {
        $this->setResource( 'default' );
        $this->setObjectName( '\\base\\model\\Customer' );
    }
    
    
    public function search($opts=array()) {
        $params = array();
 
        $queryCompanies = true;
        
        if ($queryCompanies) {
            $sql1 = "select company_id id, 'company' as type, company_name as name, contact_person, c.coc_number, c.vat_number, iban, bic, edited, created
                    from customer__company c ";
            
            $where1 = array();
            $where1[] = " c.deleted = false ";
            if (isset($opts['name']) && trim($opts['name'])) {
                $where1[] = ' c.company_name like ? ';
                $params[] = '%'.$opts['name'].'%';
            }
            
            if (isset($opts['iban']) && $opts['iban']) {
                $where1[] = ' c.iban = ? ';
                $params[] = $opts['iban'];
            }
            
            if (isset($opts['contact_person']) && $opts['contact_person']) {
                $where1[] = ' c.contact_person LIKE ? ';
                $params[] = '%'.str_replace(' ', '%', $opts['contact_person']).'%';
            }
        }
        
        
        $queryPersons = true;
        if (isset($opts['contact_person']) && $opts['contact_person']) {
            $queryPersons = false;
        }
        
        $where2 = array();
        $sql2='';
        if ($queryPersons) {
            $where2[] = " customer__person.deleted = false ";
            
            $sql2 = "select person_id id, 'person' as type, concat(lastname, ', ', insert_lastname, ' ', firstname) as name, '' as contact_person, '' as coc_number, '' as vat_number, iban, bic, edited, created
                    from customer__person ";
            if (isset($opts['name']) && trim($opts['name'])) {
                $where2[] = ' concat(lastname, \', \', insert_lastname, \' \', firstname, \' \', insert_lastname, \' \', lastname) like ? ';
                $params[] = '%'.str_replace(' ', '%', $opts['name']).'%';
            }
            
            if (isset($opts['iban']) && $opts['iban']) {
                $where2[] = ' customer__person.iban = ? ';
                $params[] = $opts['iban'];
            }
            
            if (isset($opts['contact_person'])) {
                $where2[] = ' concat(lastname, \', \', insert_lastname, \' \', firstname) like ? ';
                $params[] = '%'.$opts['contact_person'].'%';
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
        
        if ($sql2) {
            if ($sql)
                $sql .= ' union ';
            
            $sql .= $sql2;
        }
        
        
        $sql .= ' order by name ';
//         print $sql;exit;
        
        return $this->queryCursor($sql, $params);
    }
    
}

