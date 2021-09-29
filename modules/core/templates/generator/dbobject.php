<?php

$className = dbCamelCase($tableName);


?><?php print "<?php\n"; ?>


namespace <?= $moduleName ?>\model;


class <?= $className ?> extends base\<?= $className ?>Base {

	public function __construct($id=null) {
		parent::__construct( $id );
		
	}
}

