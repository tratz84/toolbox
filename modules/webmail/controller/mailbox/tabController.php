<?php

use core\controller\BaseController;
use webmail\MailTabSettings;
use webmail\form\MailTabSettingsForm;
use base\service\MetaService;


class tabController extends BaseController {
    
    
    public function action_index() {
        
        $companyId = isset($this->companyId) ? $this->companyId : null;
        $personId  = isset($this->personId)  ? $this->personId : null;
        
        $mts = new MailTabSettings( $companyId, $personId );
        
        $this->mailtabSettings = $mts->getData();
        
        $this->state = object_meta_get('user', ctx()->getUser()->getUserId(), 'customer-mailtab-splitpane-state');
        if (is_array($this->state) == false || count($this->state) != 2 || is_numeric($this->state[0]) == false || is_numeric($this->state[1]) == false) {
            $this->state = array(0.5, 0.5);
        }
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    
    public function action_settings() {
        $this->form = new MailTabSettingsForm();
        
        $companyId = get_var('companyId');
        $personId  = get_var('personId');
        
        $mts = new MailTabSettings( $companyId, $personId );
        
        $data = $mts->getData();
        $data['company_id'] = $companyId;
        $data['person_id'] = $personId;
        
        $this->form->bind( $data );
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    public function action_settings_save() {
        $form = new MailTabSettingsForm();
        $form->bind( $_REQUEST );
        
        // validate
        if ($form->validate() == false) {
            return $this->json([
                'error' => true,
                'message' => 'Form validation failed'
            ]);
        }
        
        
        $mts = new MailTabSettings( get_var('company_id'), get_var('person_id') );
        $mts->clearFilters();
        
        $mts->setApplyDefaultFilters( get_var('default_filters') ? true : false );
        /** @var \webmail\form\MailTabFilterListEdit $filters */
        $filters = $form->getWidget('filters');
        $objs = $filters->getObjects();
        foreach($objs as $o) {
            if (trim($o['filter_value']) == '')
                continue;
            
            $mts->addFilter($o['filter_type'], $o['filter_value']);
        }
        $mts->save();
        
        return $this->json([
            'success' => true
        ]);
    }
    
    
    public function action_savestate() {
        $state = array();
        
        $state = array();
        if (is_array($_REQUEST['percentages'])) for($x=0; $x < count($_REQUEST['percentages']) && $x < 10; $x++) {
            if (!doubleval($_REQUEST['percentages'][$x])) break;
            $state[] = $_REQUEST['percentages'][$x];
        }
        
        
        $user = $this->ctx->getUser();
        
        object_meta_save('user', $user->getUserId(), 'customer-mailtab-splitpane-state', $state);
        
        print 'OK';
    }
    
}

