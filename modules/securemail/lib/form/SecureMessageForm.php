<?php

namespace securemail\form;

class SecureMessageForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->hideSubmitButtons();
		
		
		$this->addValidator('plain_message', function($form) {
		    
		    $m = $form->getWidgetValue('plain_message');
		    $m = trim($m);
		    
		    if ( $form->getWidgetValue('secure_message_id') && $m == '' )
		        return;
		    
		    if ($m == '') {
		        return 'required';
		    }
		});
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('secure_message_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\WidgetContainer('container-top');
		$this->addWidget( $w2 );
		
		$w3 = new \core\forms\HtmlField('link_to_message', NULL, t('Link'));
		$w2->addWidget( $w3 );
		$w4 = new \core\forms\FieldSetContainer('container-message', t('Message'));
		$this->addWidget( $w4 );
		
		$w5 = new \customer\forms\CustomerTableSelectWidget('customer_id');
		$w4->addWidget( $w5 );
		$w6 = new \core\forms\TextField('short_description', NULL, t('Short description'));
		$w4->addWidget( $w6 );
		$w7 = new \core\forms\HtmlField('notice_message', NULL, t('Message'));
		$w4->addWidget( $w7 );
		$w8 = new \core\forms\TextareaField('plain_message', NULL, t('Message'));
		$w4->addWidget( $w8 );
		$w9 = new \core\forms\FieldSetContainer('container-enc-settings', t('Encryption settings'));
		$this->addWidget( $w9 );
		
		$w10 = new \core\forms\SelectField('ttl_type', NULL, ['expires' => t('Expires on date'), 
		'once' => t('Open once'), 
		], t('Expiry'));
		$w9->addWidget( $w10 );
		$w11 = new \core\forms\DateTimePickerField('ttl_expires_on', NULL, t('Expires on'));
		$w9->addWidget( $w11 );
		$w12 = new \core\forms\CheckboxField('save_key', NULL, t('Save key'));
		$w9->addWidget( $w12 );
		$w13 = new \core\forms\HtmlDatetimeField('deleted', NULL, t('Deleted'));
		$w9->addWidget( $w13 );
		$w13->setValue( "Nee" );
		
	}











}

