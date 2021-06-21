<?php



use customer\service\CompanyService;
use customer\service\CustomerService;
use customer\service\PersonService;
use core\controller\BaseController;
use project\form\ProjectHourForm;
use project\model\ProjectHour;
use project\service\ProjectService;

class projectHourController extends BaseController {
    
    public function init() {
        $this->addTitle( t('Projects') );
    }

    
    public function action_index() {
        $projectService = $this->oc->get(ProjectService::class);
        
        $this->project_id = $this->date = '';

        $this->company_id = get_var('company_id');
        $this->person_id = get_var('person_id');
        
        
        if (get_var('project_id')) {
            $this->project = $projectService->readProject( get_var('project_id') );
            if (!$this->project) {
                throw new \core\exception\ObjectNotFoundException('Project not found');
            }
            $this->project_id = $this->project->getProjectId();
            
            $this->company_id = $this->project->getCompanyId();
            $this->project->getCompanyId();
            $this->person_id =  $this->project->getPersonId();
        }
        if ($this->company_id) {
            $companyService = $this->oc->get(CompanyService::class);
            $company = $companyService->readCompany($this->company_id);
            if ($company) {
                $this->company_id = $company->getCompanyId();
                $this->customerName = $company->getCompanyName();
            }
        }
        if ($this->person_id) {
            $personService = $this->oc->get(PersonService::class);
            $person = $personService->readPerson($this->person_id);
            if ($person) {
                $this->person_id = $person->getPersonId();
                $this->customerName = $person->getFullname();
            }
        }
        
        if (get_var('date') && valid_date(get_var('date'))) {
            $this->date = get_var('date');
        }
        
        if (isset($this->project)) {
            $this->addTitle( t('Project') . ' ' . $this->project->getProjectName() );
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
    
    
    /**
     * action_search_project() - ProjectHourForm search
     */
    public function action_search_project() {
        $projectService = $this->oc->get(ProjectService::class);
        
        $r = $projectService->searchProject(0, 20, array('q' => get_var('name')));
        
        $arr = array();
        
        if (isset($_REQUEST['name']) == false || trim($_REQUEST['name']) == '') {
            $arr[] = array(
                'id' => '0',
                'text' => 'Maak uw keuze'
            );
        }
        foreach($r->getObjects() as $project) {
            $name = '';
            
            if ($project['company_id']) {
                $name = $project['company_name'];
            } else {
                $name = function_exists('format_personname') ? format_personname($project) : '?';
            }
            
            $name = $name . ' - ' . $project['project_name'];
            
            $arr[] = array(
                'id' => $project['project_id'],
                'text' => $name
            );
        }
        
        $result = array();
        $result['results'] = $arr;
        
        $this->json($result);
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
            $ph->setUserId( ctx()->getUser()->getUserId() );
        }
        
        if (is_get()) {
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
        }
        
        $form = new ProjectHourForm( $this->company_id, $this->person_id );
        $form->bind($ph);
        
        if (isset($this->project_id) && $this->project_id) {
            $form->getWidget('project_id')->bindObject(['project_id' => $this->project_id]);
        }
        
        if (is_post()) {
            $form->bind($_POST);
            
            if ($form->validate()) {
                $ph = $projectService->saveProjectHour($form);
                
                $project_id = $form->getWidgetValue('project_id');
                
                report_user_message(t('Changes saved'));
                
                redirect('/?m=project&c=projectHour&a=edit&project_hour_id='.$ph->getProjectHourId());
            }
        }
        
        $this->isNew = $ph->isNew();
        $this->form = $form;
        
        
        if ($this->isNew) {
            $this->addTitle(t('New hour'));
        } else {
            $this->addTitle(t('Edit hour'));
        }
        
        $this->render();
    }
    
    public function action_delete() {
        $projectService = $this->oc->get(ProjectService::class);

        $ph = $projectService->readHour(get_var('project_hour_id'));
        
        $projectService->deleteHour( get_var('project_hour_id') );
        
        redirect('/?m=project&c=projectHour&project_id='.$ph->getProjectId());
    }
    
}

