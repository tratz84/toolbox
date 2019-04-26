<?php


use core\controller\BaseController;
use core\forms\lists\ListResponse;
use project\form\ProjectHourStatusForm;
use project\model\ProjectHourStatus;
use project\service\ProjectService;

class projectHourStatusController extends BaseController {
    
    
    public function action_index() {
        
        
        $this->render();
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $projectService = $this->oc->get(ProjectService::class);
        if ($id) {
            $projectStatus = $projectService->readHourStatus($id);
        } else {
            $projectStatus = new ProjectHourStatus();
        }
        
        
        $form = new ProjectHourStatusForm();
        $form->bind($projectStatus);
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            if ($form->validate()) {
                $projectService->saveHourStatus($form);
                
                redirect('/?m=project&c=projectHourStatus');
            }
            
        }
        
        
        
        $this->isNew = $projectStatus->isNew();
        $this->form = $form;
        
        $this->render();
    }
    
    public function action_search() {
        $projectService = $this->oc->get(ProjectService::class);
        
        $hourStatus = $projectService->readHourStatuses();
        
        $list = array();
        foreach($hourStatus as $hs) {
            $list[] = $hs->getFields(array('project_hour_status_id', 'description', 'default_selected'));
        }
        
        
        $lr = new ListResponse(0, count($hourStatus), count($hourStatus), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    public function action_sort() {
        if (isset($_REQUEST['ids'])) {
            $ids = explode(',', $_REQUEST['ids']);
            
            $ps = $this->oc->get(ProjectService::class);
            $ps->updateHourStatusSort($ids);
            
        }
        
        print 'OK';
    }
    
    public function action_delete() {
        $projectService = $this->oc->get(ProjectService::class);
        $projectService->deleteHourStatus($_REQUEST['id']);
        
        redirect('/?m=project&c=projectHourStatus');
    }
    
}


