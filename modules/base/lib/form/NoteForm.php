<?php

namespace base\form;

use core\forms\validator\NotEmptyValidator;
use core\forms\validator\NumberValidator;

class NoteForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->addValidator('company_id', function($form) {
		    // save by ref_object & ref_id ?
		    $by_ref = $form->getWidgetValue('save_by_ref') ? true : false;
		    if ($by_ref) {
		        return;
		    }
		    
		    $cid = $form->getWidgetValue('company_id');
		    $pid = $form->getWidgetValue('person_id');
		    
		    if (!$cid && !$pid) {
		        return t('No company or person set');
		    }
		});

	    $this->addValidator('ref_object', function($form) {
	        // save by ref_object & ref_id ?
	        $by_ref = $form->getWidgetValue('save_by_ref') ? true : false;
	        if ($by_ref == false) {
	            return;
	        }
	        
	        $ref_object = $form->getWidgetValue('ref_object');
	        $ref_id     = $form->getWidgetValue('ref_id');
	        
	        if (!$ref_object && !$ref_id) {
	            return t('No reference object set');
	        }
	    });
        
		$this->addValidator('shortNote', function($form) {
		    $t1 = $form->getWidgetValue('shortNote');
		    $t2 = $form->getWidgetValue('longNote');
		    
		    if (trim($t1) == '' && trim($t2) == '') {
		        return t('No note entered');
		    }
		    
		});
		
		$this->disableSubmit();
		$this->hideSubmitButtons();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('save_by_ref', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w1->setInfoText( t('Save note by ref-object instead of company/person-id?') );
		$w2 = new \core\forms\HiddenField('note_id', NULL, t('Hidden field'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\HiddenField('ref_object', NULL, t('Hidden field'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\HiddenField('ref_id', NULL, t('Hidden field'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\HiddenField('company_id', NULL, t('Hidden field'));
		$this->addWidget( $w5 );
		$w6 = new \core\forms\HiddenField('person_id', NULL, t('Hidden field'));
		$this->addWidget( $w6 );
		$w7 = new \core\forms\CheckboxField('important', NULL, t('Important'));
		$this->addWidget( $w7 );
		$w7->setInfoText( t('Indien gemarkeerd als belangrijk zal de notitie worden getoond bij het bekijken/openen van de klant') );
		$w8 = new \core\forms\TextField('shortNote', NULL, t('Short description'));
		$this->addWidget( $w8 );
		$w9 = new \core\forms\TextareaField('longNote', NULL, t('Long note'));
		$this->addWidget( $w9 );
		
	}










}

