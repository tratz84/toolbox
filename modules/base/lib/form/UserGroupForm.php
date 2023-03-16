<?php

namespace base\form;

class UserGroupForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('user_group_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('parent_user_group_id', NULL, [], t('Parent group'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('group_name', NULL, t('Group name'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\WidgetContainer('container-permissions');
		$this->addWidget( $w4 );
		
	}


}

