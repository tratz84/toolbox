<?php


namespace invoice\model\base;


class InvoiceStatusBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__invoice_status' );
		$this->setPrimaryKey( 'invoice_status_id' );
		$this->setDatabaseFields( array (
  'invoice_status_id' => 
  array (
    'Field' => 'invoice_status_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'description' => 
  array (
    'Field' => 'description',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'default_selected' => 
  array (
    'Field' => 'default_selected',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'sort' => 
  array (
    'Field' => 'sort',
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
	
		
	public function setInvoiceStatusId($p) { $this->setField('invoice_status_id', $p); }
	public function getInvoiceStatusId() { return $this->getField('invoice_status_id'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setDefaultSelected($p) { $this->setField('default_selected', $p); }
	public function getDefaultSelected() { return $this->getField('default_selected'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

