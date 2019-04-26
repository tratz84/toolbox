<?php


namespace base\model;


class Company extends base\CompanyBase {
    
    protected $addressList = array();
    protected $emailList = array();
    protected $phoneList = array();
    
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setDeleted(false);
        $this->setCompanyTypeId(null);
    }
    
    
    public function getAddressList() { return $this->addressList; }
    public function setAddressList($l) { $this->addressList = $l; }
    
    public function getEmailList() { return $this->emailList; }
    public function setEmailList($l) { $this->emailList = $l; }
    
    public function getPhoneList() { return $this->phoneList; }
    public function setPhoneList($l) { $this->phoneList = $l; }
    
    
    public function getEditedFormat($f='d-m-Y H:i:s') {
        return format_datetime($this->getEdited(), $f);
    }
    
    public function getCreatedFormat($f='d-m-Y H:i:s') {
        return format_datetime($this->getCreated(), $f);
    }


}

