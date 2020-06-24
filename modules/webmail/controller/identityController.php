<?php


use core\controller\BaseController;
use core\forms\lists\ListResponse;
use webmail\service\EmailService;
use webmail\model\Identity;
use webmail\form\IdentityForm;

class identityController extends BaseController {
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_search() {
        
        $emailService = $this->oc->get(EmailService::class);
        
        $identities = $emailService->readAllIdentities();
        
        $list = array();
        foreach($identities as $i) {
            $list[] = $i->getFields(array('identity_id', 'from_name', 'from_email', 'active', 'system_messages'));
        }
        
        
        $lr = new ListResponse(0, count($list), count($list), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
        
    }
    
    public function action_edit() {
        
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $emailService = $this->oc->get(EmailService::class);
        if ($id) {
            $identity = $emailService->readIdentity($id);
        } else {
            $identity = new Identity();
        }
        
        
        $identityForm = new IdentityForm();
        $identityForm->bind($identity);
        
        if (is_post()) {
            $identityForm->bind($_REQUEST);
            
            if ($identityForm->validate()) {
                $emailService->saveIdentity( $identityForm );
                
                redirect('/?m=webmail&c=identity');
            }
        }
        
        $this->isNew = $identity->isNew();
        $this->form = $identityForm;
        
        
        $this->render();
    }
    
    public function action_delete() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $emailService = $this->oc->get(EmailService::class);
        
        $emailService->deleteIdentity( $id );
        
        redirect('/?m=webmail&c=identity');
    }
    
    
    public function action_sort() {
        
        $ids = $_REQUEST['ids'];

        $emailService = $this->oc->get(EmailService::class);
        
        $emailService->updateIdentitySort($ids);
        
        print 'OK';
    }
    
    

}

