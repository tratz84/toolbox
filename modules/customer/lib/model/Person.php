<?php


namespace customer\model;


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
    
    public function getEmailAddressNo($pos=0) {
        $els = $this->getEmailList();
        
        if ($pos >= 0 && $pos < count($els)) {
            return $els[$pos]->getEmailAddress();
        }
        else {
            return null;
        }
    }
    
    /**
     * 
     * @param int $pos - starts at 0
     * @param string $email
     */
    public function setEmailAddressNo( $pos, $email ) {
        $els = $this->getEmailList();
        
        if ($pos == 0 && count($els) == 0) {
            $els[] = new Email();
        }
        
//         while ( count($els) < $pos+1 ) {
//             $el = new Email();
//             $els[] = $el;
//         }
        
        $els[$pos]->setEmailAddress( trim($email) );
    }
    
    
    
    public function getFullname() {
        return format_personname($this);
    }
    
    public function getFullnameSpoken() {
        $n = trim( $this->getFirstname() );
        if ( trim($this->getInsertLastname()) != '' ) {
            $n .= ' ' . $this->getInsertLastname();
        }
        if ( trim($this->getLastname()) != '' ) {
            $n .= ' ' . $this->getLastname();
        }
        
        return $n;
    }
    
}

