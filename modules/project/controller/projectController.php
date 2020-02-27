<?php


use core\controller\BaseController;
use project\form\ProjectForm;
use project\model\Project;
use project\service\ProjectService;
use core\exception\ObjectNotFoundException;

class projectController extends BaseController {
    
    public function init() {
        $this->addTitle( t('Projects') );
    }
    
    
    public function action_index() {
        
        
        $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $projectService = $this->oc->get(ProjectService::class);
        
        $r = $projectService->searchProject($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        $this->json($arr);
    }
        
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $projectService = $this->oc->get(ProjectService::class);
        if ($id) {
            $project = $projectService->readProject($id);
        } else {
            $project = new Project();
        }
        
        
        $form = new ProjectForm();
        $form->bind($project);
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            if ($form->validate()) {
                $projectService->saveProject($form);
                
                redirect('/?m=project&c=project');
            }
            
        }
        
        
        
        $this->isNew = $project->isNew();
        $this->form = $form;
        
        $this->render();
        
    }
    
    
    public function action_delete() {
        $projectService = $this->oc->get(ProjectService::class);
        $projectService->deleteProject(get_var('id'));
        
        redirect('/?m=project&c=project');
    }
    
    
}

