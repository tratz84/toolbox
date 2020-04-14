<?php

use base\service\MetaService;
use core\container\ActionContainer;
use core\controller\BaseController;
use core\exception\ObjectNotFoundException;
use core\forms\lists\ListResponse;
use webmail\MailTabSettings;
use webmail\MailboxSearchSettings;
use webmail\form\MailboxSearchSettingsForm;
use webmail\mail\MailProperties;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrMailQuery;
use core\forms\SelectField;
use webmail\solr\SolrMail;
use webmail\mail\SolrMailActions;

class searchController extends BaseController {

    
    
	public function action_index() {
	    $metaService = object_container_get(MetaService::class);
	    $user = $this->ctx->getUser();
	    
	    $this->state = @unserialize( $metaService->getMetaValue('user', $user->getUserId(), 'mailbox-search-state') );
	    
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
	    
	    if (get_var('action')) {
	        $smq->addFacetSearch('action', ':', get_var('action'));
	    }
	    
	    if (get_var('folder')) {
	        $smq->addFacetSearch('mailboxName', ':', get_var('folder'));
	    }
	    
	    
	    $mss = null;
	    
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
    	    $arr['filters'] = array();
    	    $arr['filters']['folders'] = $smq->getFolders();
    	    
    	    // MailboxSearchSettings set? => apply filters
    	    if ($mss) {
    	        // folders to hide?
    	        $listFoldersToHide = $mss->getHideFolderNameList();
    	        $arr['filters']['folders'] = array_filter($arr['filters']['folders'], function($f) use ($listFoldersToHide) {
    	            if (in_array($f['name'], $listFoldersToHide)) {
    	                return false;
    	            }
    	            else {
    	                return true;
    	            }
    	        });
    	    }
    	    
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
	    
	    $this->url_view_mail = appUrl( '/?m=webmail&c=mailbox/mail&a=view&id=' . $emailId );
	    
	    // action buttons for e-mail
	    $this->actionContainer = new ActionContainer('mail-actions', null);
	    
	    
	    $f = get_data_file_safe('webmail/inbox', substr($emailId, strlen('/webmail/inbox')));
	    if (!$f) {
	        throw new ObjectNotFoundException('Mail not found');
	    }
	    
	    
	    // forward/reply buttons
	    $this->actionContainer->addItem('mail-forward', '<button class="btn-forward-mail" onclick="forwardMail('.esc_json_attr($emailId).');"><span class="fa fa-forward"></span>Forward</button>');
	    $this->actionContainer->addItem('mail-reply', '<button class="btn-reply-mail" onclick="replyMail('.esc_json_attr($emailId).');"><span class="fa fa-reply"></span>Reply</button>');
	    
	    
	    
	    $solrMail = SolrMailQuery::readStaticById($emailId);
	    
	    if ($solrMail == null) {
	        $this->setTemplateFile( module_file('webmail', 'templates/mailbox/search/not-found.php') );
	        return $this->render();
	    }
	    
	    $mp = $solrMail->getProperties();
	    if ($mp->getSeen() == false) {
	        try {
    	        $sma = new SolrMailActions();
    	        $sma->markAsSeen($solrMail);
	        } catch (\Exception|\Error $ex) { }
	    }
	    
	    
	    // move to folder
	    if ($mp->getConnectorId()) {
	        /** @var ConnectorService $connectorService */
	        $connectorService = object_container_get(ConnectorService::class);
	        /** @var Connector $connector */
	        $connector = $connectorService->readConnector($mp->getConnectorId());
	        
	        $mapFolders = array();
	        if ($connector) foreach($connector->getImapfolders() as $if) {
	            $mapFolders[$if->getFolderName()] = $if->getFolderName();
	        }
	        
	        $selectFolders = new SelectField('move_imap_folder', $mp->getFolder(), $mapFolders, null, ['add-unlisted' => true]);
	        $selectFolders->setAttribute('onchange', 'moveMail('.json_encode($emailId).', this.value)');
	        
	        $this->actionContainer->addItem('move-mail-to-folder', $selectFolders->render());
	    }

	    // Action-state
	    $mapActions = array();
	    $mapActions[ SolrMail::ACTION_OPEN ]      = t('Open');
	    $mapActions[ SolrMail::ACTION_URGENT ]    = t('Urgent');
	    $mapActions[ SolrMail::ACTION_INPROGRESS ]= t('In progress');
	    $mapActions[ SolrMail::ACTION_POSTPONED ] = t('Postponed');
	    $mapActions[ SolrMail::ACTION_DONE ]      = t('Done');
	    $mapActions[ SolrMail::ACTION_REPLIED ]   = t('Replied');
	    $mapActions[ SolrMail::ACTION_IGNORED ]   = t('Ignored');
	    $selectActions = new SelectField('set_action', $mp->getAction(), $mapActions);
	    $selectActions->setAttribute('onchange', 'setMailAction('.json_encode($emailId).', this.value)');
	    $this->actionContainer->addItem('set-mail-action', $selectActions->render());
	    

	    if ($mp->isJunk() == false) {
    	    $spam_onclick = "if (confirm('Are you sure to mark this mail as spam?')) markMailAsSpam(".json_encode($emailId).");";
    	    $this->actionContainer->addItem('mark-as-spam', '<button title="'.esc_attr(t('Mark as spam')).'" onclick="' . esc_attr($spam_onclick) . '"><span class="fa fa-flag mark-as-spam"></span></button>');
	    }

	    // delete-button
	    $delete_onclick = "if (confirm('Are you sure to delete this mail?')) deleteMail(".json_encode($emailId).");";
	    $this->actionContainer->addItem('delete-mail', '<button title="'.esc_attr(t('Delete mail')).'" onclick="' . esc_attr($delete_onclick) . '"><span class="fa fa-trash delete-mail"></span></button>');
	    
	    
	    hook_eventbus_publish($this->actionContainer, 'webmail', 'mailbox-search');
	    
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

