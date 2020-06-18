<?php


namespace customer\model;


class Customer extends \core\db\DBObject {
    
    protected $addressList = array();
    protected $emailList = array();
    protected $phoneList = array();
    
    protected $company = null;
    protected $person = null;
    
    public function __construct($id=null) {
        $this->setResource( 'default' );
        $this->setTableName( '' );
        $this->setPrimaryKey( '' );
        $this->setDatabaseFields( array ( ) );
        
    }
    
    public function getAddressList() { return $this->addressList; }
    public function setAddressList($l) { $this->addressList = $l; }
    
    public function getEmailList() { return $this->emailList; }
    public function setEmailList($l) { $this->emailList = $l; }
    
    public function getPhoneList() { return $this->phoneList; }
    public function setPhoneList($l) { $this->phoneList = $l; }
    
    public function getCompany() { return $this->company; }
    public function setCompany($c) { $this->company = $c; }
    
    public function getPerson() { return $this->person; }
    public function setPerson($p) { $this->person = $p; }
    
    public function setId($p) { $this->setField('id', $p); }
    public function getId() { return $this->getField('id'); }
    
    
    public function setType($p) { $this->setField('type', $p); }
    public function getType() { return $this->getField('type'); }

    public function setName($p) { $this->setField('name', $p); }
    public function getName() { return $this->getField('name'); }
    
    public function getPersonName() {
        $str = '';
        $str = trim($str . ' ' . $this->getField('firstname'));
        $str = trim($str . ' ' . trim($this->getField('insert_lastname')));
        $str = trim($str . ' ' . trim($this->getField('lastname')));
        
        return $str;
    }
    
    
    
    public function setByCompany(Company $c) {
        $this->setFields($c->getFields());
        
        $this->setId($c->getCompanyId());
        $this->setType('company');
        $this->setName($c->getCompanyName());
        
        $this->setAddressList( $c->getAddressList() );
        $this->setEmailList( $c->getEmailList() );
        $this->setPhoneList( $c->getPhoneList() );
        $this->setCompany( $c );
    }
    
    public function setByPerson(Person $p) {
        $this->setFields($p->getFields());
        
        $this->setId($p->getPersonId());
        $this->setType('person');
        $this->setName($p->getFullname());
        
        $this->setAddressList( $p->getAddressList() );
        $this->setEmailList( $p->getEmailList() );
        $this->setPhoneList( $p->getPhoneList() );
        $this->setPerson( $p );
    }
    
    
    
    public function getEditedFormat($f='d-m-Y H:i:s') {
        return format_datetime($this->getField('edited'), $f);
    }
    
    public function getCreatedFormat($f='d-m-Y H:i:s') {
        return format_datetime($this->getField('created'), $f);
    }
    
    
    public function __toString() {
        return $this->getName();
    }
    
}

