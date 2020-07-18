<?php

namespace project\form;

use base\service\UserService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\DateTimePickerField;
use core\forms\DynamicSelectField;
use core\forms\HiddenField;
use core\forms\RadioField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\DateTimeValidator;
use core\forms\validator\NotEmptyValidator;
use project\service\ProjectService;
use project\model\ProjectHour;
use base\forms\UserSelectWidget;

class ProjectHourForm extends BaseForm {
    
    protected $customerName = null;
    
    public function __construct($company_id, $person_id) {
        parent::__construct();
        
        $this->addKeyField('project_hour_id');
        
        $this->addWidget(new HiddenField('project_hour_id'));
        
        $this->addWidget(new UserSelectWidget() );
//         $this->addUsers();
        
//         $this->addWidget(new CheckboxField('declarable', '', 'Declarabel'));
        $this->addWidget(new RadioField('declarable', '', ['y' => t('Yes'), 'n' => t('No')], 'Declarabel'));
        
        if ($company_id) {
            $companyService = ObjectContainer::getInstance()->get(\customer\service\CompanyService::class);
            $company = $companyService->readCompany($company_id);
//             $this->addWidget(new HtmlField('company_name', $company->getCompanyName(), 'Bedrijfsnaam'));
            $this->customerName = $company->getCompanyName();
        }
        if ($person_id) {
            $personService = ObjectContainer::getInstance()->get(\customer\service\PersonService::class);
            $person = $personService->readPerson($person_id);
//             $this->addWidget(new HtmlField('person_name', $person->getFullname(), 'Naam'));
            $this->customerName = $person->getFullname();
        }
        
        $this->addWidget(new ProjectSelectWidget());
        
        
        $this->addProjectHourType();
        $this->addProjectHourStatus();
        
        $this->addWidget(new RadioField('registration_type', '', array('from_to' => 'Van tot', 'duration' => 'Duur'), 'Soort registratie'));
        
        $this->addWidget(new DateTimePickerField('start_time', '', 'Start'));
        $this->addWidget(new DateTimePickerField('end_time', '', 'Einde'));
        $this->addWidget(new TextField('duration', '', 'Duur'));
        
        $this->addWidget(new TextField('short_description', '', 'Korte omschrijving'));
        $this->addWidget(new TextareaField('long_description', '', 'Lange omschrijving'));
        
        
        $this->addValidator('declarable', function($form) {
            $v = $form->getWidgetValue('declarable');
            if ($v !== 'y' && $v !== 'n') {
                return t('required');
            }
        });
        
        $this->addValidator('short_description', new NotEmptyValidator());
        
        $this->addValidator('project_hour_type_id', new NotEmptyValidator());
        
        $this->addValidator('start_time', new DateTimeValidator());
        $this->addValidator('start_time', function($form) {
        });
        
        $this->addValidator('project_id', new NotEmptyValidator());
        
        $this->addValidator('end_time', function($form) {
            if ($form->getWidgetValue('registration_type') == 'from_to') {
                $dtv = new DateTimeValidator();
                if (!$dtv->validate($form->getWidget('end_time')))
                    return $dtv->getMessage();
                
                $ymdEnd = (int)format_datetime($form->getWidgetValue('end_time'), 'Ymd');
                $hisEnd = (int)format_datetime($form->getWidgetValue('end_time'), 'His');
                $ymdStart = (int)format_datetime($form->getWidgetValue('start_time'), 'Ymd');
                $hisStart = (int)format_datetime($form->getWidgetValue('start_time'), 'His');
                
                
                if ($ymdEnd < $ymdStart || ($ymdEnd == $ymdStart && $hisEnd < $hisStart)) {
                    return 'Eind ligt voor start';
                }
            }
            
            $startTime = $form->getWidgetValue('start_time');
            $endTime = $form->getWidgetValue('end_time');
            if (valid_datetime($startTime) && valid_datetime($endTime)) {
                $minuts = timediff_minuts($startTime, $endTime);
                
                // meer dan 10 uur in 1 ruk?
                if ($minuts && $minuts > 10*60) {
                    return 'Start/eindtijd periode langer dan 10 uur';
                }
            }
            
            
            return null;
        });
        
        $this->addValidator('duration', function($form) {
            if ($form->getWidgetValue('registration_type') == 'duration') {
                $d = intval(strtodouble($form->getWidgetValue('duration'))*100);
                
                if ($d <= 0) {
                    return 'Ongeldige duur opgegeven';
                }
            }
            
            return null;
        });
        
    }
    
    
    
    public function bind($obj) {
        parent::bind( $obj );
        
        if (is_a($obj, ProjectHour::class) && $obj->isNew() == false) {
            if ($obj->getDeclarable()) {
                $this->getWidget('declarable')->setValue('y');
            }
            else {
                $this->getWidget('declarable')->setValue('n');
            }
        }
        
    }
    
    
    protected function addProjectHourType() {
        $projectService = ObjectContainer::getInstance()->get(ProjectService::class);
        
        
        $types = $projectService->readHourTypes();
        $mapTypes = array();
        $mapTypes[''] = 'Maak uw keuze';
        
        foreach($types as $t) {
            $mapTypes[$t->getProjectHourTypeId()] = $t->getDescription();
        }
        
        $this->addWidget(new SelectField('project_hour_type_id', '', $mapTypes, 'Uursoort'));
    }
    
    protected function addProjectHourStatus() {
        $projectService = ObjectContainer::getInstance()->get(ProjectService::class);
        
        $status = $projectService->readHourStatuses();
        $mapStatus = array();
        
        foreach($status as $s) {
            $mapStatus[$s->getProjectHourStatusId()] = $s->getDescription();
        }
        
        $this->addWidget(new SelectField('project_hour_status_id', '', $mapStatus, 'Status'));
    }
    
}
