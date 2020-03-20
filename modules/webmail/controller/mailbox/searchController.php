<?php

use core\controller\BaseController;
use webmail\solr\SolrMailQuery;

class searchController extends BaseController {

    
    
	public function action_index() {
	    
		$this->render();
	}
	

	public function action_search() {
	    
	    $smq = new SolrMailQuery();
	    
	    
	    $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
	    $limit = $this->ctx->getPageSize();
	    
	    $smq->setStart($pageNo * $limit);
	    $smq->setRows( $limit );
	    
	    $lr = $smq->searchListResponse();
	    
	    $arr = array();
	    $arr['listResponse'] = $lr;
	    
	    
	    $this->json($arr);
	}


}

