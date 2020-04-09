<?php

namespace webmail\form;

class WebmailUpdateActionForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->disableSubmit();
		$this->hideSubmitButtons();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\SelectField('old_action', NULL, array (
		  'open' => 'Open',
		  'urgent' => 'Urgent',
		  'inprogress' => 'In progress',
		  'postponed' => 'Postponed',
		  'done' => 'Done',
		  'replied' => 'Replied',
		  'ignored' => 'Ignored',
		), t('Old action'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('new_action', NULL, array (
		  'open' => 'Open',
		  'urgent' => 'Urgent',
		  'inprogress' => 'In progress',
		  'postponed' => 'Postponed',
		  'done' => 'Done',
		  'replied' => 'Replied',
		  'ignored' => 'Ignored',
		), t('New action'));
		$this->addWidget( $w2 );
		
	}


}

