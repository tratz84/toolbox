<?php


namespace calendar\form;


use core\forms\BaseForm;
use core\forms\validator\NotEmptyValidator;

class CalendarForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->codegen();
        
        $this->addValidator('name', new NotEmptyValidator());
    }
    
    

	function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('calendar_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\CheckboxField('active', NULL, t('Active'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('name', NULL, t('Name'));
		$this->addWidget( $w3 );
		
	}







}