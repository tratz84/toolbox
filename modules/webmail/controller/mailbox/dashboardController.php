<?php



use base\model\User;
use core\controller\BaseController;
use webmail\MailboxSearchSettings;
use webmail\form\MailboxDashboardSettingsForm;
use webmail\solr\SolrMailQuery;

class dashboardController extends BaseController {
    
    
    public function action_index() {
        
        $this->action_search( ['render' => false] );
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    public function action_search( $opts = array() ) {
        if (isset($opts['render']) == false) $opts['render'] = true;
        
        $smq = new SolrMailQuery();
        $smq->setRows( 25 );
        $smq->setSort('date desc');
        
        $webmailSettings = object_meta_get(User::class, ctx()->getUser()->getUserId(), 'webmail-dashboard');
        $mss = new MailboxSearchSettings(null, ['data' => $webmailSettings]);
        $mss->applyFilters($smq);
        
        $this->mails = array();
        
        try {
            $this->listResponse = $smq->searchListResponse();
            
            $this->mails = $this->listResponse->getObjects();
        } catch(\Exception $ex) {
            $this->error = $ex->getMessage();
        }
        
        if ($opts['render']) {
            $this->json([
                'success' => true,
                'mails' => $this->mails
            ]);
        }
    }
    
    
    public function action_settings() {
        
        $formData = object_meta_get(User::class, ctx()->getUser()->getUserId(), 'webmail-dashboard');
        if (is_array($formData) == false) {
            $formData = array();
        }
        
        $this->form = new MailboxDashboardSettingsForm();
        $this->form->bind( $formData );
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    public function action_settings_save() {
        
        $formData = array();
        $formData['includeFilters'] = get_var('includeFilters');
        $formData['excludeFilters'] = get_var('excludeFilters');
        
        object_meta_save(User::class, ctx()->getUser()->getUserId(), 'webmail-dashboard', $formData);
        
        return $this->json(['success' => true]);
    }
    
    
}
