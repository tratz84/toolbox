<?php



use base\model\User;
use core\controller\BaseController;
use webmail\MailboxSearchSettings;
use webmail\form\MailboxSearchSettingsForm;
use webmail\solr\SolrMailQuery;

class dashboardController extends BaseController {
    
    
    public function action_index() {
        
        $smq = new SolrMailQuery();
        $smq->setRows( 25 );
        $smq->setSort('date desc');
        
        $webmailSettings = object_meta_get(User::class, ctx()->getUser()->getUserId(), 'webmail-dashboard');
        $mss = new MailboxSearchSettings(null, ['data' => $webmailSettings]);
        $mss->applyFilters($smq);
        
        try {
            $this->listResponse = $smq->searchListResponse();
            
        } catch(\Exception $ex) {
            $this->error = $ex->getMessage();
        }
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    
    public function action_settings() {
        
        $formData = object_meta_get(User::class, ctx()->getUser()->getUserId(), 'webmail-dashboard');
        if (is_array($formData) == false) {
            $formData = array();
        }
        
        $this->form = new MailboxSearchSettingsForm();
        $this->form->bind( $formData );
        $this->form->disableSubmit();
        $this->form->hideSubmitButtons();
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    public function action_settings_save() {
        
        $formData = array();
        $formData['includeFilters'] = get_var('includeFilters');
        $formData['excludeFilters'] = get_var('excludeFilters');
        
        object_meta_save(User::class, ctx()->getUser()->getUserId(), 'webmail-dashboard', $formData);
    }
    
    
}