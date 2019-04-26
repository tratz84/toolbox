<?php


use core\controller\BaseController;
use invoice\form\ArticleGroupForm;
use invoice\model\ArticleGroup;
use invoice\service\ArticleService;

class articleGroupController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    
    public function action_index() {
        
        
        $this->render();
    }
    
    public function action_search() {
        
        $locationService = $this->oc->get(ArticleService::class);
        
        $r = $locationService->searchArticleGroup(0, $this->ctx->getPageSize(), $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    
    public function action_edit() {
        $id = isset($_REQUEST['article_group_id'])?(int)$_REQUEST['article_group_id']:0;
        
        /**
         * @var LocationService
         */
        $articleService = $this->oc->get(ArticleService::class);
        if ($id) {
            $group = $articleService->readArticleGroup($id);
        } else {
            $group = new ArticleGroup();
        }
        
        
        $groupForm = new ArticleGroupForm();
        $groupForm->bind($group);
        
        if (is_post()) {
            $groupForm->bind($_REQUEST);
            
            if ($groupForm->validate()) {
                $articleService->saveArticleGroup($groupForm);
                
                redirect('/?m=invoice&c=articleGroup');
            }
        }
        
        
        
        $this->isNew = $group->isNew();
        $this->form = $groupForm;
        
        
        $this->render();
    }
    
    public function action_delete() {
        $id = isset($_REQUEST['article_group_id'])?(int)$_REQUEST['article_group_id']:0;
        
        $articleService = $this->oc->get(ArticleService::class);
        
        $articleService->deleteArticleGroup($id);
        
        redirect('/?m=invoice&c=articleGroup');
    }
}