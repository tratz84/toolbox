<?php


namespace base\model;


class EmailDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Email' );
	}
	
	
	public function readByCompany($company_id) {
	    $sql = "select *
        	    from customer__email e
        	    join customer__company_email ce on (ce.email_id = e.email_id)
        	    where ce.company_id = ?
                order by ce.sort";
	    
	    return $this->queryList($sql, array($company_id));
	}

	
	public function readByPerson($person_id) {
	    $sql = "select *
        	    from customer__email e
        	    join customer__person_email ce on (ce.email_id = e.email_id)
        	    where ce.person_id = ?
                order by ce.sort";
	    
	    return $this->queryList($sql, array($person_id));
	}
	
	public function saveForCompany($companyId, $list) {
	    $ceDao = new CompanyEmailDAO();
	    
	    $emailIds = array();
	    
	    for($x=0; $x < count($list); $x++) {
	        $l = $list[$x];
	        
	        if ($l->save()) {
	            $emailIds[] = $l->getEmailId();
	            $ceDao->insertOrUpdate($l->getField('company_email_id'), $companyId, $l->getField('email_id'), $x);
	        }
	    }
	    
	    $sql = "delete from customer__email where email_id in (select email_id from customer__company_email where company_id = ?) ";
	    if (count($emailIds))
	        $sql .= " and email_id not in (".implode(',', $emailIds).") ";
	        
        $this->query($sql, array($companyId));
	}
	
	public function delete($emailId) {
	    $this->query("delete from customer__email where email_id = ?", array($emailId));
	}
	
	public function readByEmail($email) {
	    $sql = "select e.*, ce.company_id, pe.person_id
                from customer__email e
                left join customer__company_email ce on (ce.email_id = e.email_id)
                left join customer__person_email pe on (pe.email_id = e.email_id)
                where email_address = ?";
	    
	    return $this->queryList($sql, array( $email ));
	}
}

