<?php



use admin\controller\AdminBaseController;
use admin\service\AdminExceptionService;
use core\exception\AuthorizationException;
use core\exception\SecurityException;

class exceptionController extends AdminBaseController {
    
    public function init() {
        parent::init();
        
        if (is_standalone_installation()) {
            if (!\core\Context::getInstance()->getUser()->isAdmin())
                throw new SecurityException('Permission denied');
        }
        
    }
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_search() {
        $user = $this->ctx->getUser();
        
        
        $opts = $_REQUEST;
        
        if ($user->getUserType() != 'admin') {
            foreach($user->getCustomers() as $c) {
                $contexts[] = $c->getField('contextName');
            }
            
            $opts['contextNames'] = $contexts;
        }
        
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = 15;
        
        $aeService = $this->oc->get(AdminExceptionService::class);
        
        $r = $aeService->search($pageNo*$limit, $limit, $opts);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    public function action_popup() {
        
        $id = (int)$_REQUEST['id'];
        
        $aeService = $this->oc->get(AdminExceptionService::class);
        $this->ex = $aeService->readException($id);
        
        if (is_standalone_installation() == false) {
            if (!$this->ctx->getUser()->permissionToContext($this->ex->getContextName())) {
                throw new AuthorizationException('No permission to exception');
            }
        }
        
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    
}