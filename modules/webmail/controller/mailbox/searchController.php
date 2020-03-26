<?php

use core\controller\BaseController;
use webmail\solr\SolrMailQuery;
use base\service\MetaService;
use core\forms\lists\ListResponse;
use webmail\MailTabSettings;

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
        
        $mts = null;
        if (get_var('mailtab')) {
            $companyId = get_var('company_id');
            $personId = get_var('person_id');
            
            $mts = new MailTabSettings($companyId, $personId);
            
	        // no filters? => don't show anything..
            if (
                ($mts->applyDefaultFilters() == false && $mts->getFilterCount() == 0)
                    ||
                ($mts->applyDefaultFilters() == true && count($mts->getDefaultFilters()) == 0 && $mts->getFilterCount() == 0)
                ) {
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
            $smq->setSort('score desc, date desc');
	        
	    }
	    if ($mts) {
            $smq->setMailTabSettings( $mts );
	    }
	    
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

