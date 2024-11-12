<?php


namespace customer\model;


class UserCompanyPerson extends base\UserCompanyPersonBase {

	public function __construct($id=null) {
		parent::__construct( $id );
		
	}
	
	
	
	public function readByUserId( $userId ) {
	    return $this->readBy('select * from customer__user_company_person where user_id = ?', array($userId));
	}
	
	
	public function getCustomerId() {
	    if ( $this->getCompanyId() ) {
	        return 'company-'.$this->getCompanyId();
	    }
	    if ($this->getPersonId()) {
	        return 'person-'.$this->getPersonId();
	    }
	    
	    return '';
	}
	
	
}

