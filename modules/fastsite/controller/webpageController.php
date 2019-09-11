<?php


use core\controller\BaseController;
use fastsite\data\FastsiteSettings;
use fastsite\form\WebpageForm;
use fastsite\model\Webpage;
use fastsite\model\WebpageDAO;
use fastsite\service\WebpageService;

class webpageController extends BaseController {
    
    
    public function action_index() {
        
        return $this->render();
    }
    
    
    public function action_edit() {
        $webpageService = object_container_get(WebpageService::class);
        
        if (get_var('id')) {
            $webpage = $webpageService->readWebpage( get_var('id') );
        } else {
            $webpage = new Webpage();
        }
        
        $this->form = new WebpageForm();
        $this->form->bind($webpage);
        $this->isNew = $webpage->isNew();
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                $webpageService->saveWebpage( $this->form );
                redirect('/?m=fastsite&c=webpage');
            }
        }
        
        
        return $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $webpageService = object_container_get(WebpageService::class);
        
        $r = $webpageService->searchWebpage($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        $this->json($arr);
    }
    
    public function action_delete() {
        $wDao = new WebpageDAO();
        
        
    }
    
    
}
