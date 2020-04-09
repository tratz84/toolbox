<?php

namespace webmail\form;

class MailboxHideFolderListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'objects';

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('folder_name', NULL, 'Foldername');
		$this->addWidget( $w1 );
		
	}



}

