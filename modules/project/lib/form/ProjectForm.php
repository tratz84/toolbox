<?php


namespace project\form;

use customer\service\CompanyService;
use customer\service\PersonService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\validator\NotEmptyValidator;
use project\model\Project;

class ProjectForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('project_id');
        
        $this->codegen();
        
//         $this->addWidget(new HiddenField('project_id'));
//         $this->addWidget(new CheckboxField('active', '', 'Actief'));
        
        
        $this->getWidget('project_hours')->setMin(-1);
        
        
        $this->addValidator('project_name', new NotEmptyValidator());
        $this->addValidator('customer_id', new NotEmptyValidator());
        
    }

	function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('project_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\CheckboxField('active', NULL, t('Active'));
		$this->addWidget( $w2 );
		$w3 = new \customer\forms\CustomerSelectWidget('customer_id', NULL, NULL, NULL, t('Klant'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\TextField('project_name', NULL, t('Name'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\RadioField('project_billable_type', NULL, ['fixed' => t('Fixed price'), 
		'ongoing' => t('Ongoing'), 
		], t('Project type'));
		$this->addWidget( $w5 );
		$w6 = new \core\forms\NumberField('project_hours', NULL, t('Max. hours'));
		$this->addWidget( $w6 );
		$w7 = new \core\forms\EuroField('hourly_rate', NULL, t('Hourly rate'));
		$this->addWidget( $w7 );
		$w8 = new \core\forms\TextareaField('note', NULL, t('Note'));
		$this->addWidget( $w8 );
		
	}







}

