<?php



use admin\controller\AdminBaseController;
use core\exception\AuthorizationException;

class reportController extends AdminBaseController {
    
    
    
    public function action_index() {
        $user = $this->ctx->getUser();
        
        redirect('/?m=admin&c=report&a=offer');
    }
    
    
    public function action_offer() {
        $user = $this->ctx->getUser();
        
        $this->customers = $user->getCustomers();
        
        $this->contextNames = array();
        foreach($this->customers as $c) {
            $this->contextNames[] = $c->getField('contextName');
        }
        
        
        $this->render();
    }
    
    public function action_requestOffers() {
        if (!$this->ctx->getUser()->permissionToContext($_REQUEST['contextName'])) {
            throw new AuthorizationException('No permission to context');
        }
        
        $contextName = $_REQUEST['contextName'];
        
        // request users in administration
        $opts = array();
        $opts['headers'] = array(
            'API-KEY: ' . API_KEY
        );
        $data = get_url( BASE_URL . '/' . $contextName . '/?m=invoice&c=api/offer&a=lastChanged', $opts );
        
        $objData = json_decode($data);
        
        if ($objData === false) {
            print 'Invalid response';
        }
        
        header('Content-type: application/json');
        print $data;
    }
    
}



