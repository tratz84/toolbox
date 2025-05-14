<?php



use core\controller\BaseController;
use mollie\service\MollieService;
use mollie\list\MolliePaymentIndexTable;

class paymentlistController extends BaseController {
    
    
    public function action_index() {
        
        $this->it = new MolliePaymentIndexTable();
        
        return $this->render();
    }
    
    
    
    
    public function action_search() {
        
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = ctx()->getPageSize();
        
        $mservice = object_container_get( MollieService::class );
        $r = $mservice->searchPayments( $pageNo*$limit, $limit, $_REQUEST );
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        return $this->json($arr);
    }
    
    
    public function action_view() {
        
        
        return $this->render();
    }
    
    
}


