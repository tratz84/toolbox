<?php



use base\service\MetaService;
use core\controller\BaseController;
use webmail\service\EmailService;
use webmail\form\EmailForm;

class emailController extends BaseController {
    
    public function init() {
        $this->addTitle(t('E-mail'));
    }
    
    public function action_index() {
        
        
        $user = $this->ctx->getUser();
        
        $metaService = $this->oc->get(MetaService::class);
        
        $this->state = @unserialize( $metaService->getMetaValue('user', $user->getUserId(), 'webmail-state') );
        
        $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $emailService = $this->oc->get(EmailService::class);
        
        $_REQUEST['orderby'] = 'email_id desc';
        $_REQUEST['incoming'] = false;
        $r = $emailService->searchEmail($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_view( ){
        $emailService = $this->oc->get(EmailService::class);
        $email = $emailService->readEmail($_REQUEST['id']);
        
        $this->form = new EmailForm();
        $this->form->bind( $email );
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    public function action_delete() {
        $emailService = $this->oc->get(EmailService::class);
        
        $emailService->deleteEmail((int)$_REQUEST['id']);
        
        redirect('/?m=webmail&c=email');
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
        $metaService->saveMeta('user', $user->getUserId(), 'webmail-state', serialize($state));
        
        print 'OK';
    }
    
}
