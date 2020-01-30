<?php


use core\controller\BaseController;
use invoice\form\ArticleForm;
use invoice\model\Article;
use invoice\service\ArticleService;

class articleController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $articleService = $this->oc->get(ArticleService::class);
        
        $r = $articleService->searchArticles($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        /**
         * @var LocationService
         */
        $articleService = $this->oc->get(ArticleService::class);
        if ($id) {
            $a = $articleService->readArticle($id);
        } else {
            $a = new Article();
        }
        
        
        $aForm = $this->oc->create(ArticleForm::class);
        $aForm->bind($a);
        
        if (is_post()) {
            $aForm->bind($_REQUEST);
            
            
            if ($aForm->validate()) {
                $a = $articleService->saveArticle($aForm);
                
                redirect('/?m=invoice&c=article');
            }
        }
        
        
        
        $this->isNew = $a->isNew();
        $this->form = $aForm;
        
        
        $this->render();
    }
    
    
    public function action_delete() {
        $articleService = $this->oc->get(ArticleService::class);
        
        $articleService->deleteArticle((int)$_REQUEST['id']);
        
        
        redirect('/?m=invoice&c=article');
    }
    
    
    public function action_popup() {
        
        $this->setShowDecorator(false);
        
        $this->render();
    }
    
    
    
    public function action_select2() {
        
        $articleService = $this->oc->get(ArticleService::class);
        
        $opts = $_REQUEST;
        if (isset($opts['name']) && isset($opts['article_name']) == false)
            $opts['article_name'] = $opts['name'];
        
        $r = $articleService->searchArticles(0, 20, $opts);
        
        
        $arr = array();
        
        if (isset($_REQUEST['article_id']) == false || trim($_REQUEST['article_id']) == '') {
            $arr[] = array(
                'id' => '0',
                'text' => 'Maak uw keuze'
            );
        }
        foreach($r->getObjects() as $article) {
            $arr[] = array(
                'id' => $article['article_id'],
                'text' => $article['article_name'],
                'price' => $article['price']
            );
        }
        
        
        $result = array();
        $result['results'] = $arr;
        
        $this->json($result);
            
        
    }
    
    
}

