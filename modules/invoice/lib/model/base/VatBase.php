<?php


namespace invoice\model\base;


class VatBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__vat' );
		$this->setPrimaryKey( 'vat_id' );
		$this->setDatabaseFields( array (
  'vat_id' => 
  array (
    'Field' => 'vat_id',
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
  'percentage' => 
  array (
    'Field' => 'percentage',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'visible' => 
  array (
    'Field' => 'visible',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
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
	
		
	public function setVatId($p) { $this->setField('vat_id', $p); }
	public function getVatId() { return $this->getField('vat_id'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setPercentage($p) { $this->setField('percentage', $p); }
	public function getPercentage() { return $this->getField('percentage'); }
	
		
	public function setVisible($p) { $this->setField('visible', $p); }
	public function getVisible() { return $this->getField('visible'); }
	
		
	public function setDefaultSelected($p) { $this->setField('default_selected', $p); }
	public function getDefaultSelected() { return $this->getField('default_selected'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

