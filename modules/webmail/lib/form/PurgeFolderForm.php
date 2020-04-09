<?php

namespace webmail\form;

class PurgeFolderForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->disableSubmit();
		$this->hideSubmitButtons();
		
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return mapAllConnectors(); }; 
		
		$w1 = new \core\forms\SelectField('connectorId', NULL, $func1(), t('Connector'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('folderName', NULL, array (
		  '' => 'Make your choice',
		  'junk' => 'Junk',
		  'trash' => 'Trash',
		), t('Folder'));
		$this->addWidget( $w2 );
		
	}


}

