<?php

namespace codegen\form;

class IndexTableColumnListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'htmlColumns';

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('column_name', NULL, 'Column name');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextareaField('column_html', NULL, 'Column html');
		$this->addWidget( $w2 );
		
	}







}

