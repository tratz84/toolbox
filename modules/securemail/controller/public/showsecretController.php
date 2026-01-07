<?php



use core\controller\BaseController;
use securemail\service\SecureMailService;
use securemail\model\SecureMessage;

class showsecretController extends BaseController {
    
    
    
    public function action_index() {
        $id = (int)get_var('smid');
        
        $this->setShowDecorator(false);
        $this->delurl = '';
        
        // fetch message
        $smservice = object_container_get( SecureMailService::class );
        $sm = $smservice->readMessage( $id );
        
        // check errors
        $this->error = $this->check_errors($sm);
        if ($this->error != null) {
            return $this->render();
        }
        
        // expires? => show immediately
        if ($sm && $sm->getTtlType() == 'expires') {
            $p = @base64_decode( get_var('p') );
            $msg = $sm->getDecryptedMessage($p);
            $sm->setPlainMessage( $msg );
        }
        
        
        $this->sm = $sm;
        $this->delurl = appUrl('/?m=securemail&c=public/showsecret&a=del&smid='.$sm->getSecureMessageId().'&p='.urlencode(get_var('p')));
        
        return $this->render();
    }
    
    public function action_del() {
        
        $this->setShowDecorator(false);
        
        $id = (int)get_var('smid');
        
        $smservice = object_container_get( SecureMailService::class );
        $sm = $smservice->readMessage( $id );
        
        if ( $sm->checkPassword( @base64_decode(get_var('p')) ) ) {
            $smservice->markDeleted( $sm->getSecureMessageId(), remote_addr(), 'Marked as deleted through remote-link' );
        }
        
        
        return $this->render();
    }
    
    
    protected function check_errors( SecureMessage $sm ) {
        if (!$sm) {
            return t('Message not found');
        }
        if ( valid_datetime($sm->getDeleted()) ) {
            return t('Message removed');
        }
        
        $p = @base64_decode( get_var('p') );
        if ( $sm->checkPassword($p) == false ) {
            return t('Invalid secret');
        }
        
        return null;
    }
    
    
    public function action_request_otc() {
        
        
    }
    
    
}


