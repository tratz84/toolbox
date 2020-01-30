<?php

namespace payment\form;

class PaymentLineListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'PaymentLines';

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return payment_method_map(); }; 
		
		$w1 = new \core\forms\SelectField('payment_method_id', NULL, $func1(), 'Betaalmethode');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('description1', NULL, 'Opmerking');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\EuroField('euro', NULL, 'Bedrag');
		$this->addWidget( $w3 );
		
	}








}

