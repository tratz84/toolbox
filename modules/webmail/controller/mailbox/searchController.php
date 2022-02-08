<?php

use base\service\MetaService;
use core\controller\BaseController;
use core\forms\lists\ListResponse;
use webmail\MailTabSettings;
use webmail\MailboxSearchSettings;
use webmail\form\MailboxSearchSettingsForm;
use webmail\solr\SolrMailQuery;
use webmail\search\MailSearchBase;

class searchController extends BaseController {

    
    
	public function action_index() {
	    $metaService = object_container_get(MetaService::class);
	    $user = $this->ctx->getUser();
	    
	    $this->state = @unserialize( $metaService->getMetaValue('user', $user->getUserId(), 'mailbox-search-state') );
	    
	    $this->filtersEnabled = isset($_GET['filters']) == false || $_GET['filters'] ? true : false;
	    
	    hook_htmlscriptloader_enableGroup('webmail');
	    
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
	    
	    
	    $mq = MailSearchBase::getInstance();
	    
	    $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
	    $limit = $this->ctx->getPageSize();
	    
	    $mq->setStart($pageNo * $limit);
	    $mq->setRows( $limit );
	    
	    if (get_var('q')) {
	        $mq->setQuery( get_var('q') );
//             $smq->setSort('score desc, date desc');
	    }
	    
	    if (get_var('action')) {
	        $mq->addAction( get_var('action') );
	    }
	    
	    if (get_var('folder')) {
	        $mq->addMailboxName( get_var('folder') );
	    }
	    
	    
	    $mss = null;
	    
	    // TODO: hmz... this isn't the right way
	    if ($mts) {
	        $mq->applyMailTabSettings( $mts );
	    } else {
	        if (get_var('f')) {
    	        $mss = new MailboxSearchSettings();
	            $mq->applyMailboxSearchSettings( $mss );
	        }
	    }
	    
	    try {
    	    $lr = $mq->searchListResponse();
    	    
    	    $mq_folders = MailSearchBase::getInstance();
    	    $mq_folders->setRows(0);
    	    $mq_folders->searchListResponse();
    	    
    	    
    	    $arr = array();
    	    $arr['listResponse'] = $lr;
    	    
    	    
    	    // MailboxSearchSettings set? => apply filters
    	    $listFoldersToHide = array();
    	    if ($mss) {
    	        // folders to hide?
    	        $listFoldersToHide = $mss->getHideFolderNameList();
    	    }
    	    
    	    $arr['filters'] = array();
	        $arr['filters']['folders'] = $mq_folders->getFolders( ['filter' => $listFoldersToHide] );
	        
    	    $this->json($arr);
	    } catch(\Exception $ex) {
	        $this->json([
	            'error' => true,
	            'message' => $ex->getMessage()
            ]);
	    }
	}
	
	
	public function action_view() {
	    
	    $this->setShowDecorator(false);
	    
	    $this->emailId = $emailId = get_var('id');
	    
	    $this->url_view_mail = appUrl( '/?m=webmail&c=mailbox/mail&skip_mailactions=1&a=view&id=' . $emailId );
	    
	    
	    
	    return $this->render();
	}
	
	
	
	
	public function action_savestate() {
	    
	    $ps = get_var('percentages');
	    
	    $state = array();
	    $state['filterWidth'] = isset($ps['filterWidth']) ? $ps['filterWidth'] : 0.1;
	    $state['mailHeaders'] = isset($ps['mailHeaders']) ? $ps['mailHeaders'] : 0.3;
	    $state['mailContent'] = isset($ps['mailContent']) ? $ps['mailContent'] : 0.7;
	    
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
	    $mss->clearHideFolders();
	    
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
	    
	    
	    /** @var \webmail\form\MailboxHideFolderListEdit $hideFolders */
	    $hideFolderList = $form->getWidget('hideFolderList');
	    $objs = $hideFolderList->getObjects();
	    foreach($objs as $o) {
	        if (trim($o['folder_name']) == '')
	            continue;
	            
            $mss->addHideFolders( $o['folder_name'] );
	    }
	    
	    
	    $mss->save();
	    
	    return $this->json([
	        'success' => true
	    ]);
	}
	

}

