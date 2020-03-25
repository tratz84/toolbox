<?php

use core\controller\BaseController;
use webmail\solr\SolrMailQuery;
use base\service\MetaService;
use core\forms\lists\ListResponse;

class searchController extends BaseController {

    
    
	public function action_index() {
	    $metaService = object_container_get(MetaService::class);
	    $user = $this->ctx->getUser();
	    
	    $this->state = @unserialize( $metaService->getMetaValue('user', $user->getUserId(), 'mailbox-search-state') );
	    
	    
		$this->render();
	}
	

	public function action_search() {
	    
        $mailtabSettings = @json_decode( get_var('mailtabSettings', true) );
        if (is_object($mailtabSettings)) $mailtabSettings = (array)$mailtabSettings;
        
	    if (is_post()) {
	        // check if mailtabSettings is set
	        $emptyResult = false;
	        if ($mailtabSettings == false) {
	            $emptyResult = true;
	        } if (is_array($mailtabSettings)) {
	            if (count($mailtabSettings) == 0) {
	                $emptyResult = true;
	            }
	            if (isset($mailtabSettings['email']) && count($mailtabSettings['email']) == 0) {
	                $emptyResult = true;
	            }
	        }
	        
	        if ($emptyResult) {
	            return $this->json([
	                'listResponse' => new ListResponse(0, 0, 0, [])
	            ]);
	        }
	    }
	    
	    
	    $smq = new SolrMailQuery();
	    
	    $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
	    $limit = $this->ctx->getPageSize();
	    
	    $smq->setStart($pageNo * $limit);
	    $smq->setRows( $limit );
	    
	    if (get_var('q')) {
	        $smq->setQuery( get_var('q') );
	    }
        $smq->setMailTabSettings( $mailtabSettings );
	    
	    try {
    	    $lr = $smq->searchListResponse();
    	    
    	    $arr = array();
    	    $arr['listResponse'] = $lr;
    	    $this->json($arr);
	    } catch(\Exception $ex) {
	        $this->json([
	            'error' => true,
	            'message' => $ex->getMessage()
            ]);
	    }
	}
	
	
	public function action_savestate() {
	    $state = array();
	    
	    $state['slider-ratio'] = array();
	    if (is_array($_REQUEST['percentages'])) for($x=0; $x < count($_REQUEST['percentages']) && $x < 10; $x++) {
	        if (!doubleval($_REQUEST['percentages'][$x])) break;
	        $state['slider-ratio'][] = $_REQUEST['percentages'][$x];
	    }
	    
	    
	    $user = $this->ctx->getUser();
	    
	    $metaService = $this->oc->get(MetaService::class);
	    $metaService->saveMeta('user', $user->getUserId(), 'mailbox-search-state', serialize($state));
	    
	    print 'OK';
	}


}

