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
        
        $opts = $_REQUEST;
        
        // default sorting
        if (isset($opts['sortField']) == false) {
            $opts['sortField'] = 'last_visit';
            $opts['sortFieldDirection'] = 'desc';
        }
        
        $r = $tfService->searchCookies($pageNo*$limit, $limit, $opts);
        
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


