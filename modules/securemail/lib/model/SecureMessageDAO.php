<?php


namespace securemail\model;

use core\db\query\QueryBuilderWhere;
use core\db\query\QueryBuilderWhereContainer;


class SecureMessageDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\securemail\\model\\SecureMessage' );
	}
	
	
	public function read($id) {
	    $sql = "select * from securemail__secure_message where secure_message_id = ?";
	    
	    return $this->queryOne($sql, array($id));
	}
	
	
	public function search($opts) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->setTable('securemail__secure_message');
	    $qb->selectField('secure_message_id', 'securemail__secure_message');
	    $qb->selectField('encryption_algo',   'securemail__secure_message');
	    $qb->selectField('ttl_type',          'securemail__secure_message');
	    $qb->selectField('ttl_expires_on',    'securemail__secure_message');
	    $qb->selectField('short_description', 'securemail__secure_message');
	    $qb->selectField('person_id',         'securemail__secure_message');
	    $qb->selectField('company_id',        'securemail__secure_message');
	    $qb->selectField('person_id',         'securemail__secure_message');
	    $qb->selectField('password',          'securemail__secure_message');
	    $qb->selectField('edited',            'securemail__secure_message');
	    $qb->selectField('created',           'securemail__secure_message');
	    
	    $qb->selectField('company_name',      'customer__company');
	    $qb->selectField('firstname',         'customer__person');
	    $qb->selectField('insert_lastname',   'customer__person');
	    $qb->selectField('lastname',          'customer__person');
	    
	    $qb->leftJoin('customer__company', 'company_id');
	    $qb->leftJoin('customer__person', 'person_id');
	    
	    if (isset($opts['name']) && $opts['name']) {
	        
	        $qbwc = new QueryBuilderWhereContainer('OR');
	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal('company_name', 'LIKE', '%' . $opts['name'] . '%'));
	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal("concat_ws('', customer__person.firstname, ' ', customer__person.insert_lastname, ' ', customer__person.lastname)", 'LIKE', '%' . $opts['name'] . '%'));
	        
	        $qb->addWhere($qbwc);
	    }
	    
	    if (isset($opts['q']) && $opts['q']) {
	        
	        $q = trim($opts['q']);
	        $q = '%'.preg_replace('/\\s+/', '%', $q).'%';
	        
	        $qbwc = new QueryBuilderWhereContainer('OR');
	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal("concat_ws('', company_name, ' ', project__project.project_name)", 'LIKE', $q));
	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal("concat_ws('', customer__person.firstname, ' ', customer__person.insert_lastname, ' ', customer__person.lastname, ' ', project__project.project_name)", 'LIKE', $q));
	        
	        $qb->addWhere($qbwc);
	    }
	    
	    if (isset($opts['short_description']) && $opts['short_description']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('short_description', 'LIKE', '%' . $opts['short_description'] . '%'));
	    }
	    
	    $qb->setRawOrderBy("secure_message_id desc");
	    
	    return $qb->queryCursor( SecureMessage::class );
	}

}

