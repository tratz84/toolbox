<?php



use core\controller\BaseController;
use securemail\service\SecureMailService;
use securemail\model\SecureMessage;
use securemail\model\SecureMessageLog;

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
            $sml = new SecureMessageLog();
            $sml->setSecureMessageId( $sm->getSecureMessageId() );
            $sml->setOpened(false);
            $sml->setLogMessage('Request error: ' . $this->error);
            $sml->setIp( remote_addr() );
            $sml->save();
            
            return $this->render();
        }
        
        // expires? => show immediately
        if ($sm && $sm->getTtlType() == 'expires') {
            $p = @base64_decode( get_var('p') );
            $msg = $sm->getDecryptedMessage($p);
            $sm->setPlainMessage( $msg );
            
            // log
            $sml = new SecureMessageLog();
            $sml->setSecureMessageId( $sm->getSecureMessageId() );
            $sml->setOpened(true);
            $sml->setLogMessage('Page requested');
            $sml->setIp( remote_addr() );
            $sml->save();
        }
        else {
            $sml = new SecureMessageLog();
            $sml->setSecureMessageId( $sm->getSecureMessageId() );
            $sml->setOpened(false);
            $sml->setLogMessage('Page requested');
            $sml->setIp( remote_addr() );
            $sml->save();
        }
        
        
        $this->sm = $sm;
        $this->delurl = appUrl('/?m=securemail&c=public/showsecret&a=del&smid='.$sm->getSecureMessageId().'&p='.urlencode(get_var('p')));
        $this->otcurl = appUrl('/?m=securemail&c=public/showsecret&a=request_otc&smid='.$sm->getSecureMessageId().'&p='.urlencode(get_var('p')));
        
        
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
        
        $id = (int)get_var('smid');
        
        $this->setShowDecorator(false);
        
        // fetch message
        $smservice = object_container_get( SecureMailService::class );
        $sm = $smservice->readMessage( $id );
        
        // check errors
        $this->error = $this->check_errors($sm);
        if ($this->error != null) {
            $sml = new SecureMessageLog();
            $sml->setSecureMessageId( $sm->getSecureMessageId() );
            $sml->setOpened(false);
            $sml->setLogMessage('Otc request error: ' . $this->error);
            $sml->setIp( remote_addr() );
            $sml->save();
            
            return $this->renderJson([
                'error' => true,
                'success' => false,
                'message' => $this->error
            ]);
        }
        
        // once - always the case.. might be changed in admin though
        if ($sm->getTtlType() == 'once') {
            $sml = new SecureMessageLog();
            $sml->setSecureMessageId( $sm->getSecureMessageId() );
            $sml->setOpened(true);
            $sml->setLogMessage('Page requested');
            $sml->setIp( remote_addr() );
            $sml->save();
            
            $smservice->markDeleted( $sm->getSecureMessageId(), remote_addr(), 'Message requested through link' );
        }
        
        
        $p = @base64_decode( get_var('p') );
        $plainMessage = $sm->getDecryptedMessage($p);
        
        
        $r = array();
        
        $r['message'] = $plainMessage;
        $r['success'] = true;
        
        print json_encode($r);
    }
    
    
}


