<?php

use core\controller\BaseController;
use filesync\service\WopiService;

class wopitokensController extends BaseController {

	public function action_index() {
	

		$this->render();
	}

	public function action_search() {
	    $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
	    $limit = $this->ctx->getPageSize();
	    
	    $wopiService = $this->oc->get(WopiService::class);
	    
	    $r = $wopiService->searchWopiAccess($pageNo*$limit, $limit, $_REQUEST);
	    
	    $arr = array();
	    $arr['listResponse'] = $r;
	    
	    
	    $this->json($arr);
	}

	public function action_delete() {
        
	    $wopiService = $this->oc->get(WopiService::class);
	    $wopiService->deleteToken( get_var('id') );
        
        $this->json([
            'success' => true
        ]);
	}
	
	
	public function action_delete_all() {
	    $wopiService = $this->oc->get(WopiService::class);
	    $wopiService->deleteAllTokens();
	    
	    redirect('/?m=filesync&c=wopitokens');
	}


}

