<?php

namespace dataimport\form;

class UploadSheetForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->enctypeToMultipartFormdata();
		$this->hideSubmitButtons();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\FileField('file', NULL, t('File'));
		$this->addWidget( $w1 );
		
	}


}

