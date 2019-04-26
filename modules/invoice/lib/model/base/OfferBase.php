<?php


namespace invoice\model\base;


class OfferBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__offer' );
		$this->setPrimaryKey( 'offer_id' );
		$this->setDatabaseFields( array (
  'offer_id' => 
  array (
    'Field' => 'offer_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'company_id' => 
  array (
    'Field' => 'company_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'person_id' => 
  array (
    'Field' => 'person_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'offer_number' => 
  array (
    'Field' => 'offer_number',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'offer_status_id' => 
  array (
    'Field' => 'offer_status_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'subject' => 
  array (
    'Field' => 'subject',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'comment' => 
  array (
    'Field' => 'comment',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'note' => 
  array (
    'Field' => 'note',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'total_calculated_price' => 
  array (
    'Field' => 'total_calculated_price',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'total_calculated_price_incl_vat' => 
  array (
    'Field' => 'total_calculated_price_incl_vat',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'accepted' => 
  array (
    'Field' => 'accepted',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'offer_date' => 
  array (
    'Field' => 'offer_date',
    'Type' => 'date',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'deposit' => 
  array (
    'Field' => 'deposit',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'payment_upfront' => 
  array (
    'Field' => 'payment_upfront',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'edited' => 
  array (
    'Field' => 'edited',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'created' => 
  array (
    'Field' => 'created',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setOfferId($p) { $this->setField('offer_id', $p); }
	public function getOfferId() { return $this->getField('offer_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setOfferNumber($p) { $this->setField('offer_number', $p); }
	public function getOfferNumber() { return $this->getField('offer_number'); }
	
		
	public function setOfferStatusId($p) { $this->setField('offer_status_id', $p); }
	public function getOfferStatusId() { return $this->getField('offer_status_id'); }
	
		
	public function setSubject($p) { $this->setField('subject', $p); }
	public function getSubject() { return $this->getField('subject'); }
	
		
	public function setComment($p) { $this->setField('comment', $p); }
	public function getComment() { return $this->getField('comment'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setTotalCalculatedPrice($p) { $this->setField('total_calculated_price', $p); }
	public function getTotalCalculatedPrice() { return $this->getField('total_calculated_price'); }
	
		
	public function setTotalCalculatedPriceInclVat($p) { $this->setField('total_calculated_price_incl_vat', $p); }
	public function getTotalCalculatedPriceInclVat() { return $this->getField('total_calculated_price_incl_vat'); }
	
		
	public function setAccepted($p) { $this->setField('accepted', $p); }
	public function getAccepted() { return $this->getField('accepted'); }
	
		
	public function setOfferDate($p) { $this->setField('offer_date', $p); }
	public function getOfferDate() { return $this->getField('offer_date'); }
	
		
	public function setDeposit($p) { $this->setField('deposit', $p); }
	public function getDeposit() { return $this->getField('deposit'); }
	
		
	public function setPaymentUpfront($p) { $this->setField('payment_upfront', $p); }
	public function getPaymentUpfront() { return $this->getField('payment_upfront'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

