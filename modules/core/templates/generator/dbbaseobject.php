<?php

$className = dbCamelCase($tableName);

$fields = array();
$pks = array();
foreach($columns as $c) {
    $fields[$c['Field']] = $c;
    
    if ($c['Key'] == 'PRI')
        $pks[] = $c['Field'];
}


?><?php print "<?php\n"; ?>


namespace <?= $moduleName ?>\model\base;


class <?= $className ?>Base extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( '<?= $databaseResource ?>' );
		$this->setTableName( '<?= $tableName ?>' );
		$this->setPrimaryKey( '<?= count($pks) == 1 ? $pks[0] : '' ?>' );
		$this->setDatabaseFields( <?= var_export($fields) ?> );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
	<?php foreach($columns as $c) : ?>
	
	public function set<?= dbCamelCase($c['Field']) ?>($p) { $this->setField('<?= $c['Field'] ?>', $p); }
	public function get<?= dbCamelCase($c['Field']) ?>() { return $this->getField('<?= $c['Field'] ?>'); }
	
	<?php endforeach; ?>

}

