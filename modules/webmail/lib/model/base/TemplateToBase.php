<?php


namespace webmail\model\base;


class TemplateToBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'mailing__template_to' );
		$this->setPrimaryKey( 'template_to_id' );
		$this->setDatabaseFields( array (
  'template_to_id' => 
  array (
    'Field' => 'template_to_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'template_id' => 
  array (
    'Field' => 'template_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'to_type' => 
  array (
    'Field' => 'to_type',
    'Type' => 'enum(\'To\',\'Cc\',\'Bcc\')',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'to_name' => 
  array (
    'Field' => 'to_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'to_email' => 
  array (
    'Field' => 'to_email',
    'Type' => 'varchar(255)',
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
	
		
	public function setTemplateToId($p) { $this->setField('template_to_id', $p); }
	public function getTemplateToId() { return $this->getField('template_to_id'); }
	
		
	public function setTemplateId($p) { $this->setField('template_id', $p); }
	public function getTemplateId() { return $this->getField('template_id'); }
	
		
	public function setToType($p) { $this->setField('to_type', $p); }
	public function getToType() { return $this->getField('to_type'); }
	
		
	public function setToName($p) { $this->setField('to_name', $p); }
	public function getToName() { return $this->getField('to_name'); }
	
		
	public function setToEmail($p) { $this->setField('to_email', $p); }
	public function getToEmail() { return $this->getField('to_email'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

