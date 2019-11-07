<?= "<?php" ?>


namespace <?= $namespace ?>;

class <?= $classname ?> extends \core\forms\ListEditWidget {

	public function __construct() {
		parent::__construct('objects');
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		
	}

}

