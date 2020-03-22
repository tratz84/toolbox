<?php


namespace project\form;

use base\service\CompanyService;
use base\service\PersonService;
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
    
    
    public function bind($obj) {
        parent::bind($obj);
        
        $companyId = null;
        $personId = null;
        
        $customerWidget = $this->getWidget('customer_id');
        
        if (is_a($obj, Project::class)) {
            $companyId = $obj->getCompanyId();
            $personId = $obj->getPersonId();
        }
        
        
        if (is_array($obj) && isset($obj['customer_id'])) {
            
            if (strpos($obj['customer_id'], 'company-') === 0) {
                $companyId = str_replace('company-', '', $obj['customer_id']);
            }
            else if (strpos($obj['customer_id'], 'person-') === 0) {
                $personId = str_replace('person-', '', $obj['customer_id']);
            }
            
        }
        
        if ($companyId) {
            $customerWidget->setValue('company-'.$companyId);
            
            $cs = ObjectContainer::getInstance()->get(CompanyService::class);
            $name = $cs->getCompanyName($companyId);
            
            $customerWidget->setDefaultText( $name );
        }
        else if ($personId) {
            $customerWidget->setValue('person-'.$personId);
            
            $ps = ObjectContainer::getInstance()->get(PersonService::class);
            $fullname = $ps->getFullname($personId);
            
            $customerWidget->setDefaultText( $fullname );
        }
    }
    

	function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('project_id', NULL, 'Hidden field');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\CheckboxField('active', NULL, 'Active');
		$this->addWidget( $w2 );
		$w3 = new \base\forms\CustomerSelectWidget('customer_id', NULL, NULL, NULL, 'Klant');
		$this->addWidget( $w3 );
		$w4 = new \core\forms\TextField('project_name', NULL, 'Name');
		$this->addWidget( $w4 );
		$w5 = new \core\forms\RadioField('project_billable_type', NULL, array (
		  'fixed' => 'Fixed price',
		  'ongoing' => 'Ongoing',
		), 'Project type');
		$this->addWidget( $w5 );
		$w6 = new \core\forms\NumberField('project_hours', NULL, 'Max. hours');
		$this->addWidget( $w6 );
		$w7 = new \core\forms\EuroField('hourly_rate', NULL, 'Hourly rate');
		$this->addWidget( $w7 );
		$w8 = new \core\forms\TextareaField('note', NULL, 'Note');
		$this->addWidget( $w8 );
		
	}






}

