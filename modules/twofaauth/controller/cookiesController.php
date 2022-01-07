<?php



use core\controller\BaseController;
use twofaauth\form\CookiesIndexTable;
use twofaauth\service\TwoFaService;

class cookiesController extends BaseController {
    
    
    public function action_index() {
        
        $this->it = new CookiesIndexTable();
        
        return $this->render();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $tfService = object_container_get( TwoFaService::class );
        
        $r = $tfService->searchCookies($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    public function action_delete() {
        $tfService = object_container_get( TwoFaService::class );
        
        $id = get_var('id');
        
        $tfService->deleteCookie( $id );
        
        redirect( appUrl('/?m=twofaauth&c=cookies') );
    }
    
}


