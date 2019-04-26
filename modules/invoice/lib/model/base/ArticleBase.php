<?php


namespace invoice\model\base;


class ArticleBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'article__article' );
		$this->setPrimaryKey( 'article_id' );
		$this->setDatabaseFields( array (
  'article_id' => 
  array (
    'Field' => 'article_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'article_type' => 
  array (
    'Field' => 'article_type',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'article_name' => 
  array (
    'Field' => 'article_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description1' => 
  array (
    'Field' => 'long_description1',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description2' => 
  array (
    'Field' => 'long_description2',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'price' => 
  array (
    'Field' => 'price',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'rentable' => 
  array (
    'Field' => 'rentable',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'simultaneously_rentable' => 
  array (
    'Field' => 'simultaneously_rentable',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'price_type' => 
  array (
    'Field' => 'price_type',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'vat_price' => 
  array (
    'Field' => 'vat_price',
    'Type' => 'bigint(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'vat_id' => 
  array (
    'Field' => 'vat_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
  ),
  'deleted' => 
  array (
    'Field' => 'deleted',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
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
	
		
	public function setArticleId($p) { $this->setField('article_id', $p); }
	public function getArticleId() { return $this->getField('article_id'); }
	
		
	public function setArticleType($p) { $this->setField('article_type', $p); }
	public function getArticleType() { return $this->getField('article_type'); }
	
		
	public function setArticleName($p) { $this->setField('article_name', $p); }
	public function getArticleName() { return $this->getField('article_name'); }
	
		
	public function setLongDescription1($p) { $this->setField('long_description1', $p); }
	public function getLongDescription1() { return $this->getField('long_description1'); }
	
		
	public function setLongDescription2($p) { $this->setField('long_description2', $p); }
	public function getLongDescription2() { return $this->getField('long_description2'); }
	
		
	public function setPrice($p) { $this->setField('price', $p); }
	public function getPrice() { return $this->getField('price'); }
	
		
	public function setRentable($p) { $this->setField('rentable', $p); }
	public function getRentable() { return $this->getField('rentable'); }
	
		
	public function setSimultaneouslyRentable($p) { $this->setField('simultaneously_rentable', $p); }
	public function getSimultaneouslyRentable() { return $this->getField('simultaneously_rentable'); }
	
		
	public function setPriceType($p) { $this->setField('price_type', $p); }
	public function getPriceType() { return $this->getField('price_type'); }
	
		
	public function setVatPrice($p) { $this->setField('vat_price', $p); }
	public function getVatPrice() { return $this->getField('vat_price'); }
	
		
	public function setVatId($p) { $this->setField('vat_id', $p); }
	public function getVatId() { return $this->getField('vat_id'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setDeleted($p) { $this->setField('deleted', $p); }
	public function getDeleted() { return $this->getField('deleted'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

