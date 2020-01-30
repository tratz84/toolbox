<?php

namespace payment\form;

use payment\service\PaymentService;

class PaymentLineListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'PaymentLines';
    
    protected static $defaultSelectedPaymentMethod = null;
    protected static $defaultSelectedPaymentMethodRead = false;
    

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
		
		if (self::$defaultSelectedPaymentMethodRead == false) {
		    $ps = object_container_get(PaymentService::class);
		    self::$defaultSelectedPaymentMethod = $ps->readDefaultSelectedPaymentMethod();
		    
		    self::$defaultSelectedPaymentMethodRead = true;
		}
		if (self::$defaultSelectedPaymentMethod) {
		    $pmid = self::$defaultSelectedPaymentMethod->getPaymentMethodId();
		    $this->getWidget('payment_method_id')->setValue( $pmid );
		}
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return payment_method_map(); }; 
		
		$w1 = new \core\forms\SelectField('payment_method_id', NULL, $func1(), 'Betaalmethode');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('description1', NULL, 'Opmerking');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\EuroField('amount', NULL, 'Bedrag');
		$this->addWidget( $w3 );
		
	}









}

