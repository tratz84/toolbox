<?php

$className = dbCamelCase($tableName);


?><?php print "<?php\n"; ?>


namespace <?= $moduleName ?>\model;


class <?= $className ?>DAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( '<?= $databaseResource ?>' );
		$this->setObjectName( '\\<?= $moduleName ?>\\model\\<?= $className ?>' );
	}
	

}

