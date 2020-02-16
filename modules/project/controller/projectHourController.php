<?php



use base\service\CompanyService;
use base\service\PersonService;
use core\controller\BaseController;
use project\form\ProjectHourForm;
use project\model\ProjectHour;
use project\service\ProjectService;

class projectHourController extends BaseController {
    
    
    public function action_index() {
        $projectService = $this->oc->get(ProjectService::class);
        
        $this->project_id = $this->company_id = $this->person_id = $this->date = '';

        if (get_var('project_id')) {
            $this->project = $projectService->readProject( get_var('project_id') );
            $this->project_id = $this->project->getProjectId();
        }
        if (get_var('company_id')) {
            $companyService = $this->oc->get(CompanyService::class);
            $company = $companyService->readCompany(get_var('company_id'));
            if ($company) {
                $this->company_id = $company->getCompanyId();
            }
        }
        if (get_var('person_id')) {
            $personService = $this->oc->get(PersonService::class);
            $person = $personService->readPerson(get_var('person_id'));
            if ($person) {
                $this->person_id = $person->getPersonId();
            }
        }
        
        if (get_var('date') && valid_date(get_var('date'))) {
            $this->date = get_var('date');
        }
        
        
        $this->render();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $projectService = $this->oc->get(ProjectService::class);
        
        $r = $projectService->searchHour($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        $this->json($arr);
    }
    
    public function action_edit() {
        $projectService = $this->oc->get(ProjectService::class);

        $this->project_id = $this->company_id = $this->person_id = null;
        
        if (get_var('project_hour_id')) {
            $ph = $projectService->readHour(get_var('project_hour_id'));
            $this->project = $projectService->readProject( $ph->getProjectId() );
            $this->project_id = $this->project->getProjectId();
            $this->company_id = $this->project->getCompanyId();
            
        } else {
            $ph = new ProjectHour();
        }
        
        if (get_var('project_id')) {
            $this->project = $projectService->readProject( get_var('project_id') );
            $this->project_id = $this->project->getProjectId();
            $this->company_id = $this->project->getCompanyId();
            $this->person_id = $this->project->getPersonId();
        }
        if (get_var('company_id')) {
            $this->company_id = (int)get_var('company_id');
        }
        if (get_var('person_id')) {
            $this->person_id = (int)get_var('person_id');
        }
        
        $form = new ProjectHourForm( $this->company_id, $this->person_id );
        $form->bind($ph);
        
        if ($this->project_id ?? false) {
            $form->getWidget('project_id')->setValue($this->project_id);
        }
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            if ($form->validate()) {
                $projectService->saveProjectHour($form);
                
                redirect('/?m=project&c=projectHour&project_id='.$this->project->getProjectId());
            }
        }
        
        $this->isNew = $ph->isNew();
        $this->form = $form;
        
        $this->render();
    }
    
    public function action_delete() {
        $projectService = $this->oc->get(ProjectService::class);

        $ph = $projectService->readHour(get_var('project_hour_id'));
        
        $projectService->deleteHour( get_var('project_hour_id') );
        
        redirect('/?m=project&c=projectHour&project_id='.$ph->getProjectId());
    }
    
}

