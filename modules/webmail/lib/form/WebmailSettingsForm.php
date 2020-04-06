<?php

namespace webmail\form;

use webmail\service\EmailTemplateService;

class WebmailSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		/** @var EmailTemplateService $emailTemplateService */
		$emailTemplateService = object_container_get( EmailTemplateService::class );
		$mapTemplates = array();
		$mapTemplates[''] = t('Make your choice');
		$templates = $emailTemplateService->readAllTemplates();
		foreach($templates as $t) {
		    $name = $t->getName();
		    if (!$name)
		        $name = $t->getTemplateCode();
		    $mapTemplates[$t->getTemplateId()] = $name;
		}
		$this->getWidget('template_id_new_mail')->setOptionItems($mapTemplates);
		$this->getWidget('template_id_reply_mail')->setOptionItems($mapTemplates);
		$this->getWidget('template_id_forward_mail')->setOptionItems($mapTemplates);
		
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\SelectField('template_id_new_mail', NULL, array (
		), t('Template new mail'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('template_id_reply_mail', NULL, array (
		), t('Template reply mail'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\SelectField('template_id_forward_mail', NULL, array (
		), t('Template forward mail'));
		$this->addWidget( $w3 );
		
	}


}

