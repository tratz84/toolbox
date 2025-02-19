<?php


namespace customer\model;



use core\db\query\QueryBuilderWhere;
use core\db\query\QueryBuilderWhereContainer;

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
        
        $qbs = apply_filter( 'customer_search_queries', $qbs );
        
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
            $limit = apply_filter('CustomerDAO::search-limit', $limit);
            
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
        
        // customer_id set?
        if (isset($opts['customer_id']) && trim($opts['customer_id']) != '') {
            if (strpos($opts['customer_id'], 'company-') === 0) {
                $queryCompanies = true;
                $queryPersons = false;
                $opts['company_id'] = substr($opts['customer_id'], strlen('company-'));
            }
            if (strpos($opts['customer_id'], 'person-') === 0) {
                $queryCompanies = false;
                $queryPersons = true;
                $opts['person_id'] = substr($opts['customer_id'], strlen('person-'));
            }
            
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

            if (isset($opts['company_id']) && $opts['company_id']) {
                $qb1->addWhere(QueryBuilderWhere::whereRefByVal('customer__company.company_id', '=', $opts['company_id']));
            }
            
            if (isset($opts['name']) && trim($opts['name'])) {
                $qb1->addWhere(QueryBuilderWhere::whereRefByVal('company_name', 'LIKE', '%'.$opts['name'].'%'));
            }
            
            if (isset($opts['iban']) && $opts['iban']) {
                $qb1->addWhere(QueryBuilderWhere::whereRefByVal('iban', 'LIKE', '%'.$opts['iban'].'%'));
            }
            
            if (isset($opts['contact_person']) && $opts['contact_person']) {
                $qb1->addWhere(QueryBuilderWhere::whereRefByVal('contact_person', 'LIKE', '%'.$opts['contact_person'].'%'));
            }
            
            if (isset($opts['q']) && $opts['q']) {
                $c = new QueryBuilderWhereContainer( 'OR' );
                $c->addWhere(QueryBuilderWhere::whereRefByVal('company_name', 'LIKE', '%'.$opts['q'].'%'));
                $c->addWhere(QueryBuilderWhere::whereRefByVal('contact_person', 'LIKE', '%'.$opts['q'].'%'));
                
                $qb1->addWhere( $c );
            }
            
        }
        
        if (isset($opts['contact_person']) && trim($opts['contact_person']) != '' && $queryCompanies == true) {
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
            $qb2->selectFunction("concat(ifnull(lastname, ''), ', ', ifnull(insert_lastname, ''), ' ', ifnull(firstname, '')) as name");
            
            $qb2->setTable('customer__person');
            
            $qb2->addWhere(QueryBuilderWhere::whereRefByVal('deleted', '=', 'false'));
            
            if (isset($opts['person_id']) && $opts['person_id']) {
                $qb2->addWhere(QueryBuilderWhere::whereRefByVal('person_id', '=', $opts['person_id']));
            }
            
            
            if (isset($opts['name']) && trim($opts['name'])) {
                $qb2->addWhere(QueryBuilderWhere::whereRefByVal(
                    " concat(ifnull(lastname, ''), ', ', ifnull(insert_lastname, ''), ' ', ifnull(firstname, '), ' ', ifnull(insert_lastname, '), ' ', lastname)"
                    , 'LIKE'
                    , '%'.str_replace(' ', '%', $opts['name']).'%'));
            }
            
            if (isset($opts['iban']) && $opts['iban']) {
                $qb2->addWhere(QueryBuilderWhere::whereRefByVal('iban', '=', $opts['iban']));
            }
            
//             if (isset($opts['contact_person']) && $opts['contact_person']) {
//                 $qb2->addWhere(QueryBuilderWhere::whereRefByVal(
//                     " concat(ifnull(lastname, ''), ', ', ifnull(insert_lastname, ''), ' ', ifnull(firstname, '), ' ', ifnull(insert_lastname, '), ' ', lastname)"
//                     , 'LIKE'
//                     , '%'.str_replace(' ', '%', $opts['contact_person']).'%'));
//             }
            
            if (isset($opts['q']) && $opts['q']) {
                $c = new QueryBuilderWhereContainer( 'OR' );
                
                $c->addWhere(QueryBuilderWhere::whereRefByVal(
                    " concat(ifnull(lastname, ''), ', ', ifnull(insert_lastname, ''), ' ', ifnull(firstname, '), ' ', ifnull(insert_lastname, '), ' ', lastname)"
                    , 'LIKE'
                    , '%'.str_replace(' ', '%', $opts['q']).'%'));
                
                $qb2->addWhere( $c );
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

