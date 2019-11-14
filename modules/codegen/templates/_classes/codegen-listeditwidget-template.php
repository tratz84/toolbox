<?= "<?php" ?>


namespace <?= $namespace ?>;

class <?= $classname ?> extends \core\forms\ListEditWidget {

    protected static $getterName = "objects";

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		
	}

}

