<?php

namespace base\form;

use core\forms\validator\NotEmptyValidator;
use core\forms\validator\NumberValidator;

class NoteForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->addValidator('company_id', function($form) {
		    $cid = $form->getWidgetValue('company_id');
		    $pid = $form->getWidgetValue('person_id');
		    
		    if (!$cid && !$pid) {
		        return t('No company or person set');
		    }
		});
		
		$this->addValidator('shortDescription', function($form) {
		    $t1 = $form->getWidgetValue('shortDescription');
		    $t2 = $form->getWidgetValue('longNote');
		    
		    if (trim($t1) == '' && trim($t2) == '') {
		        return t('No note entered');
		    }
		    
		});
		
		$this->disableSubmit();
		$this->hideSubmitButtons();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('note_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\HiddenField('ref_object', NULL, t('Hidden field'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\HiddenField('ref_id', NULL, t('Hidden field'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\HiddenField('company_id', NULL, t('Hidden field'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\HiddenField('person_id', NULL, t('Hidden field'));
		$this->addWidget( $w5 );
		$w6 = new \core\forms\CheckboxField('important', NULL, t('Important'));
		$this->addWidget( $w6 );
		$w7 = new \core\forms\TextField('shortNote', NULL, t('Short description'));
		$this->addWidget( $w7 );
		$w8 = new \core\forms\TextareaField('longNote', NULL, t('Long note'));
		$this->addWidget( $w8 );
		
	}








}

