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
	
	
	public function action_view() {
	    
	    $this->emailId = $emailId = get_var('id');
	    
	    $this->url_view_mail = appUrl( '/?m=webmail&c=mailbox/mail&a=view&id=' . $emailId );
	    
	    // action buttons for e-mail
	    $this->actionContainer = new ActionContainer('mail-actions', null);
	    
	    
	    $f = get_data_file_safe('webmail/inbox', substr($emailId, strlen('/webmail/inbox')));
	    if (!$f) {
	        throw new ObjectNotFoundException('Mail not found');
	    }
	    
	    
	    // TODO: implement..
// 	    $this->actionContainer->addItem('mail-forward', '<button class="btn-reply-mail"><span class="fa fa-forward"></span>Forward</button>');
// 	    $this->actionContainer->addItem('mail-reply', '<button class="btn-forward-mail"><span class="fa fa-reply"></span>Reply</button>');
	    
	    
	    $mp = new MailProperties( $f );
	    $mp->load();
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
	    $mapActions[ SolrMail::ACTION_POSTPONED ] = t('Postponed');
	    $mapActions[ SolrMail::ACTION_DONE ]      = t('Done');
	    $mapActions[ SolrMail::ACTION_REPLIED ]   = t('Replied');
	    $mapActions[ SolrMail::ACTION_IGNORED ]   = t('Ignored');
	    $selectActions = new SelectField('set_action', $mp->getAction(), $mapActions);
	    $selectActions->setAttribute('onchange', 'setMailAction('.json_encode($emailId).', this.value)');
	    $this->actionContainer->addItem('set-mail-action', $selectActions->render());
	    
	    
	    hook_eventbus_publish($this->actionContainer, 'webmail', 'mailbox-search');
	    
	    $this->setShowDecorator(false);
	    
	    return $this->render();
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

