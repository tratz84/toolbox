<?php


namespace base\model;


class Person extends base\PersonBase {

    protected $addressList = array();
    protected $emailList = array();
    protected $phoneList = array();
    
    protected $companyList = array();
    
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setDeleted(false);
    }
    
    
    
    public function getAddressList() { return $this->addressList; }
    public function setAddressList($l) { $this->addressList = $l; }
    
    public function getEmailList() { return $this->emailList; }
    public function setEmailList($l) { $this->emailList = $l; }
    
    public function getPhoneList() { return $this->phoneList; }
    public function setPhoneList($l) { $this->phoneList = $l; }
    
    public function getCompanyList() { return $this->companyList; }
    public function setCompanyList($l) { $this->companyList = $l; }
    
    
    public function getFullname() {
        
        return format_personname($this);
    }
    
    
}

