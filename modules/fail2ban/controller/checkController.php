<?php

use core\controller\BaseController;
use fail2ban\form\BanCheckIndexTable;
use fail2ban\service\AbuseService;
use core\container\ActionContainer;

class checkController extends BaseController {

	public function action_index() {
	
	    $this->it = new BanCheckIndexTable();
	    
	    
	    $this->ac = new ActionContainer('fail2ban', 'index');
	    $this->ac->addItem('delete-checks', '<a href="javascript:void(0);" onclick="cleanup_Click();">Clean up</a>');
	    

		$this->render();
	}

	public function action_search() {
	    $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
	    $limit = $this->ctx->getPageSize();
	    
	    $aService = object_container_get( AbuseService::class );
	    
	    $r = $aService->searchBanCheck($pageNo*$limit, $limit, $_REQUEST);
	    
	    $arr = array();
	    $arr['listResponse'] = $r;
	    
	    $this->json($arr);
	}

	public function action_delete() {
	   
	   $aService = object_container_get( AbuseService::class );
	   $aService->cleanUp( get_var('f') );
        
	   redirect( '/?m=fail2ban&c=check' );
	}


}

