<?php


namespace invoice\model\base;


class CompanySettingBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__company_setting' );
		$this->setPrimaryKey( 'company_setting_id' );
		$this->setDatabaseFields( array (
  'company_setting_id' => 
  array (
    'Field' => 'company_setting_id',
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
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'tax_shift' => 
  array (
    'Field' => 'tax_shift',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'tax_excemption' => 
  array (
    'Field' => 'tax_excemption',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'payment_term' => 
  array (
    'Field' => 'payment_term',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setCompanySettingId($p) { $this->setField('company_setting_id', $p); }
	public function getCompanySettingId() { return $this->getField('company_setting_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setTaxShift($p) { $this->setField('tax_shift', $p); }
	public function getTaxShift() { return $this->getField('tax_shift'); }
	
		
	public function setTaxExcemption($p) { $this->setField('tax_excemption', $p); }
	public function getTaxExcemption() { return $this->getField('tax_excemption'); }
	
		
	public function setPaymentTerm($p) { $this->setField('payment_term', $p); }
	public function getPaymentTerm() { return $this->getField('payment_term'); }
	
	
}

