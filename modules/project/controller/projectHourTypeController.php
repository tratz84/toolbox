<?php


use core\controller\BaseController;
use core\forms\lists\ListResponse;
use project\form\ProjectHourTypeForm;
use project\model\ProjectHourType;
use project\service\ProjectService;

class projectHourTypeController extends BaseController {
    
    public function action_index() {
        
        
        $this->render();
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $projectService = $this->oc->get(ProjectService::class);
        if ($id) {
            $projectType = $projectService->readHourType($id);
        } else {
            $projectType = new ProjectHourType();
        }
        
        
        $form = new ProjectHourTypeForm();
        $form->bind($projectType);
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            if ($form->validate()) {
                $projectService->saveHourType($form);
                
                redirect('/?m=project&c=projectHourType');
            }
            
        }
        
        
        
        $this->isNew = $projectType->isNew();
        $this->form = $form;
        
        $this->render();
    }
    
    public function action_search() {
        $projectService = $this->oc->get(ProjectService::class);
        
        $hourTypes = $projectService->readHourTypes();
        
        $list = array();
        foreach($hourTypes as $ht) {
            $list[] = $ht->getFields(array('project_hour_type_id', 'description', 'visible', 'default_selected'));
        }
        
        
        $lr = new ListResponse(0, count($hourTypes), count($hourTypes), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    public function action_delete() {
        $projectService = $this->oc->get(ProjectService::class);
        $projectService->deleteHourType($_REQUEST['id']);
        
        redirect('/?m=project&c=projectHourType');
    }
    
    
    public function action_sort() {
        if (isset($_REQUEST['ids'])) {
            $ids = explode(',', $_REQUEST['ids']);
            
            $ps = $this->oc->get(ProjectService::class);
            $ps->updateHourTypeSort($ids);
            
        }
        
        print 'OK';
    }
    
}