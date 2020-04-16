<?php

namespace project\form;

use base\service\CustomerService;
use core\forms\DynamicSelectField;
use project\model\ProjectHour;
use project\service\ProjectService;

class ProjectSelectWidget extends DynamicSelectField {
    
    protected $customerName = null;
    
    public function __construct($name='project_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = 'Select project';
        if ($endpoint == null) $endpoint = '/?m=project&c=projectHour&a=search_project';
        if ($label == null) $label = 'Project';
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $p_id = null;
        if (is_a($obj, ProjectHour::class)) {
            $p_id = $obj->getProjectId();
        }
        if (is_array($obj) && isset($obj['project_id'])) {
            $p_id = $obj['project_id'];
        }
        
        if ($p_id) {
            $projectService = object_container_get(ProjectService::class);
            $project = $projectService->readProject( $p_id );
            
            if ($this->customerName) {
                $name = $this->customerName . ' - ';
            } else {
                $customerService = object_container_get( CustomerService::class );
                $customer = $customerService->readCustomerAuto( $project->getCompanyId(), $project->getPersonId() );
                $name = $customer->getName() . ' - ';
            }
            $name = $name . $project->getProjectName();
            
            $this->setDefaultText($name);
        }
        
    }
    
}


