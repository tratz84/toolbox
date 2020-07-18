<?php


namespace customer\model;



use core\db\query\QueryBuilderWhere;

class CustomerDAO extends \core\db\DAOObject {
    
    public function __construct() {
        $this->setResource( 'default' );
        $this->setObjectName( '\\customer\\model\\Customer' );
    }
    

    public function searchCount($opts=array()) {
        $qbs = $this->searchQueries($opts);
        
        $count = 0;
        foreach($qbs as $qb) {
            $qb->clearSelectFields();
            $qb->selectFunction('count(*)');
            
            $sql = $qb->createSelect();
            $params = $qb->getParams();
            
            $count += (int)$this->queryValue($sql, $params);
        }
        
        return $count;
    }
    
    
    public function search($opts=array()) {
        $qbs = $this->searchQueries($opts);
        
        $params = array();
        $sqls = array();
        foreach($qbs as $qb) {
            $sqls[] = $qb->createSelect();
            $params = array_merge($params, $qb->getParams());
        }
        
        $sql = implode("\nUNION\n\n", $sqls);
        $sql .= ' order by name ';
        
        if (isset($opts['limit'])) {
            $start = isset($opts['start']) ? intval($opts['start']) : 0;
            $limit = intval($opts['limit']);
            
            $sql .= " LIMIT {$start}, {$limit}";
        }
        
        return $this->queryCursor($sql, $params);
    }
    
    protected function searchQueries($opts=array()) {
        $queryCompanies = true;
        $queryPersons = true;
        
        if (isset($opts['customer_type']) && $opts['customer_type'] == 'company') {
            $queryCompanies = true;
            $queryPersons = false;
        }
        if (isset($opts['customer_type']) && $opts['customer_type'] == 'person') {
            $queryCompanies = false;
            $queryPersons = true;
        }
        
        
        $qb1 = $qb2 = null;
        
        if ($queryCompanies) {
            $qb1 = $this->createQueryBuilder();
            
            $qb1->selectField('company_id',     'customer__company', 'id');
            $qb1->selectField('\'company\'',    '', 'type');
            $qb1->selectField('coc_number',     'customer__company');
            $qb1->selectField('vat_number',     'customer__company');
            $qb1->selectField('iban',           'customer__company');
            $qb1->selectField('bic',            'customer__company');
            $qb1->selectField('edited',         'customer__company');
            $qb1->selectField('created',        'customer__company');
            $qb1->selectField('contact_person', 'customer__company');
            $qb1->selectField('company_name',   'customer__company', 'name');
            
            $qb1->setTable('customer__company');
            
            $qb1->addWhere(QueryBuilderWhere::whereRefByVal('deleted', '=', 'false'));
            
            if (isset($opts['name']) && trim($opts['name'])) {
                $qb1->addWhere(QueryBuilderWhere::whereRefByVal('company_name', 'LIKE', '%'.$opts['name'].'%'));
            }
            
            if (isset($opts['iban']) && $opts['iban']) {
                $qb1->addWhere(QueryBuilderWhere::whereRefByVal('iban', 'LIKE', '%'.$opts['iban'].'%'));
            }
            
            if (isset($opts['contact_person']) && $opts['contact_person']) {
                $qb1->addWhere(QueryBuilderWhere::whereRefByVal('contact_person', 'LIKE', '%'.$opts['contact_person'].'%'));
            }
        }
        
        if (isset($opts['contact_person']) && $opts['contact_person']) {
            $queryPersons = false;
        }
        
        if ($queryPersons) {
            $qb2 = $this->createQueryBuilder();
            
            $qb2->selectField('person_id',    'customer__person', 'id');
            $qb2->selectField('\'person\'',   '', 'type');
            $qb2->selectField("'coc_number'", '', 'coc_number');
            $qb2->selectField("'vat_number'", '', 'vat_number');
            $qb2->selectField('iban',         'customer__person');
            $qb2->selectField('bic',          'customer__person');
            $qb2->selectField('edited',       'customer__person');
            $qb2->selectField('created',      'customer__person');
            $qb2->selectField("''",           '', 'contact_person');
            $qb2->selectFunction("concat(lastname, ', ', insert_lastname, ' ', firstname) as name");
            
            $qb2->setTable('customer__person');
            
            $qb2->addWhere(QueryBuilderWhere::whereRefByVal('deleted', '=', 'false'));
            
            
            if (isset($opts['name']) && trim($opts['name'])) {
                $qb2->addWhere(QueryBuilderWhere::whereRefByVal(
                    ' concat(lastname, \', \', insert_lastname, \' \', firstname, \' \', insert_lastname, \' \', lastname)'
                    , 'LIKE'
                    , '%'.str_replace(' ', '%', $opts['name']).'%'));
            }
            
            if (isset($opts['iban']) && $opts['iban']) {
                $qb2->addWhere(QueryBuilderWhere::whereRefByVal('iban', '=', $opts['iban']));
            }
            
            if (isset($opts['contact_person'])) {
                $qb2->addWhere(QueryBuilderWhere::whereRefByVal(
                    ' concat(lastname, \', \', insert_lastname, \' \', firstname, \' \', insert_lastname, \' \', lastname)'
                    , 'LIKE'
                    , '%'.str_replace(' ', '%', $opts['contact_person']).'%'));
            }
        }
        
        $qbs = array();
        if ($qb1) {
            $qbs[] = $qb1;
        }
        if ($qb2) {
            $qbs[] = $qb2;
        }
        
        return $qbs;
    }
    
}

