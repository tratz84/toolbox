<?php

use core\controller\BaseController;
use webmail\solr\SolrMailQuery;
use base\service\MetaService;
use core\forms\lists\ListResponse;
use webmail\MailTabSettings;
use core\container\ActionContainer;
use webmail\form\MailboxSearchSettingsForm;
use webmail\form\MailSettingsOutForm;
use webmail\MailboxSearchSettings;

class searchController extends BaseController {

    
    
	public function action_index() {
	    $metaService = object_container_get(MetaService::class);
	    $user = $this->ctx->getUser();
	    
	    $this->state = @unserialize( $metaService->getMetaValue('user', $user->getUserId(), 'mailbox-search-state') );
	    
	    // action buttons for e-mail
	    $this->actionContainer = new ActionContainer('mail-actions', null);
	    hook_eventbus_publish($this->actionContainer, 'webmail', 'mailbox-search');
	    
	    $this->filtersEnabled = isset($_GET['filters']) == false || $_GET['filters'] ? true : false;
	    
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
	    
	    // TODO: hmz... this isn't the right way
	    if ($mts) {
	        $mts->applyFilters( $smq );
	    } else {
	        if (get_var('f')) {
    	        $mss = new MailboxSearchSettings();
    	        $mss->applyFilters($smq);
	        }
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

	
	public function action_settings() {
	    
	    $this->form = new MailboxSearchSettingsForm();
	    
	    $mss = new MailboxSearchSettings();
	    $this->form->bind( $mss->getData() );
	    
	    $this->setShowDecorator(false);
	    
	    return $this->render();
	}
	
	public function action_settings_save() {
	    
	    $form = new MailboxSearchSettingsForm();
	    $form->bind( $_REQUEST );
	    
	    // validate
	    if ($form->validate() == false) {
	        return $this->json([
	            'error' => true,
	            'message' => 'Form validation failed'
	        ]);
	    }
	    
	    
	    $mss = new MailboxSearchSettings();
	    $mss->clearIncludeFilters();
	    $mss->clearExcludeFilters();
	    
	    /** @var \webmail\form\MailTabFilterListEdit $includeFilters */
	    $includeFilters = $form->getWidget('includeFilters');
	    $objs = $includeFilters->getObjects();
	    foreach($objs as $o) {
	        if (trim($o['filter_value']) == '')
	            continue;
            
            $mss->addIncludeFilter($o['filter_type'], $o['filter_value']);
	    }

	    /** @var \webmail\form\MailTabFilterListEdit $excludeFilters */
	    $excludeFilters = $form->getWidget('excludeFilters');
	    $objs = $excludeFilters->getObjects();
	    foreach($objs as $o) {
	        if (trim($o['filter_value']) == '')
	            continue;
            
            $mss->addExcludeFilter($o['filter_type'], $o['filter_value']);
	    }
	    
	    $mss->save();
	    
	    return $this->json([
	        'success' => true
	    ]);
	}
	

}

