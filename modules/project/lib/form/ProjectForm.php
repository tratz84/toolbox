<?php


namespace project\form;

use base\service\CompanyService;
use base\service\PersonService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\DynamicSelectField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\TextareaField;
use project\model\Project;
use core\forms\validator\NotEmptyValidator;
use core\forms\NumberField;
use core\forms\RadioField;
use core\forms\EuroField;

class ProjectForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('project_id');
        
        $this->addWidget(new HiddenField('project_id'));
        $this->addWidget(new CheckboxField('active', '', 'Actief'));
        
        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=base&c=customer&a=select2', 'Klant') );
        
        $this->addWidget(new TextField('project_name', '', 'Naam'));
        
        
        $mapProjectType = array();
        $mapProjectType['fixed'] = _('Fixed price');
        $mapProjectType['ongoing'] = _('Ongoing');
        $this->addWidget(new RadioField('project_billable_type', '', $mapProjectType, t('Project type')));
        
        $this->addWidget(new NumberField('project_hours', '', _('Max. hours')));
        $this->getWidget('project_hours')->setMin(-1);
        
        $this->addWidget(new EuroField('hourly_rate', '', t('Hourly rate')));
        
        $this->addWidget(new TextareaField('note', '', 'Notitie'));
        
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
    
}

