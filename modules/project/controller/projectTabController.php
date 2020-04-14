<?php



use core\controller\BaseController;
use project\service\ProjectService;

class projectTabController extends BaseController {
    
    
    public function action_index() {
        
        if (isset($this->companyId) == false) {
            $this->companyId = null;
        }
        if (isset($this->personId) == false) {
            $this->personId = null;
        }
        
        // no customer selected?
        if (!$this->companyId && $this->personId)
            return;
        
        
        // fetch projects for customer
        $projectService = object_container_get(ProjectService::class);
        $projects = $projectService->readByCustomer( $this->companyId, $this->personId );
        
        $this->mapProjects = array();
        $this->mapProjects[] = ['value' => '', 'text' => t('Project name') ];
        foreach($projects as $p) {
            $this->mapProjects[] = ['value' => $p->getProjectId(), 'text' => $p->getProjectName() ];
        }
        
        // fetch statuses
        $statuses = $projectService->readHourStatuses();
        $this->mapStatuses = array();
        $this->mapStatuses[] = ['value' => '', 'text' => t('Status')];
        foreach($statuses as $s) {
            $this->mapStatuses[] = ['value' => $s->getProjectHourStatusId(), 'text' => $s->getDescription()];
        }
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    
    
}
