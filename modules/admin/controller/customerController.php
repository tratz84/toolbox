<?php


use admin\controller\AdminBaseController;
use core\exception\AuthorizationException;

class customerController extends AdminBaseController {
    
    
    public function action_index() {
        
        $this->render();
    }
    
    
    public function action_popup_users() {
        
        $this->contextName = $_REQUEST['contextName'];
        
        if (!$this->ctx->getUser()->permissionToContext($this->contextName)) {
            throw new AuthorizationException('No permission to context');
        }
        
        
        // request users in administration
        $opts = array();
        $opts['headers'] = array(
            'API-KEY: ' . API_KEY
        );
        $data = get_url( BASE_URL . '/' . $_REQUEST['contextName'] . '/?m=base&c=api/user', $opts );

        $this->json = json_decode($data);
        
        
        $this->setShowDecorator(false);
        
        $this->render();
    }
    
    public function action_do_autologin() {
        
        if (!$this->ctx->getUser()->permissionToContext($_REQUEST['contextName'])) {
            throw new AuthorizationException('No permission to exception');
        }
        
        $logActivity = '0';//$this->ctx->getUser()->getUserType() == 'admin' ? '1' : '0';
        
        
        // TODO: set autologin_token for customer & redirect
        $opts = array();
        $opts['headers'] = array('API-KEY: ' . API_KEY);
        
        $response = post_url( BASE_URL . '/' . $_REQUEST['contextName'] . '/?m=base&c=api/user&a=autologin', array('username' => $_POST['username'], 'log-activity' => $logActivity), $opts );
        
        $json = json_decode($response);
        if ($json == false || isset($json->token) == false) {
            var_export($response);
            exit;
        }
        
        header('Location: /'.$_REQUEST['contextName'].'/?admin_autologin='.$json->token);
    }


}
