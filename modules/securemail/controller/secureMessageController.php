<?php


use core\controller\BaseController;
use securemail\form\SecureMessageForm;
use securemail\model\SecureMessage;
use securemail\service\SecureMailService;
use securemail\form\SecureMessageTable;
use core\exception\ObjectNotFoundException;

class secureMessageController extends BaseController {
    
    
    public function action_index() {
        $this->it = new SecureMessageTable();
        
        return $this->render();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $smService = object_container_get( SecureMailService::class );
        
        $r = $smService->searchMessage($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        $this->json($arr);
    }
    
    
    public function action_edit() {
        $this->form = new SecureMessageForm();
        
        $smservice = object_container_get( SecureMailService::class );
        
        if (get_var('sm_id')) {
            $sm = $smservice->readMessage( (int)get_var('sm_id') );
            
            if ($sm == null) {
                throw new ObjectNotFoundException( 'SecureMessage not found' );
            }
            
        }
        else {
            $sm = new SecureMessage();
        }
        
        $this->form->bind( $sm );
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ( $this->form->validate() ) {
                $sm = $smservice->saveMessage( $this->form );
                
                report_user_message( t('Changes saved') );
                if (!get_var('sm_id') && !$this->form->getWidgetValue('save_key')) {
                    $_SESSION['secure_message_key'] = array(
                        'secure_message_id' => $sm->getSecureMessageId(),
                        'password' => $sm->getPassword()
                    );
                    
                    redirect('/?m=securemail&c=secureMessage&a=show_link&sm_id='.$sm->getSecureMessageId());
                } else {
                    redirect('/?m=securemail&c=secureMessage&a=edit&sm_id='.$sm->getSecureMessageId());
                }
            }
        }
        
        $this->isNew = $sm->getSecureMessageId() ? false : true;
        
        if ($this->isNew) {
            $this->form->getWidget('container-message')->removeWidget('notice_message');
            
            $this->form->getWidget('container-top')->removeWidget('link_to_message');
        }
        else {
            
            if ($sm->isDeleted()) {
                $this->form->getWidget('container-top')->removeWidget('link_to_message');
                $this->form->getWidget('container-message')->removeWidget('plain_message');
                $this->form->getWidget('container-enc-settings')->removeWidget('save_key');
                
                $this->form->getWidget('container-message')->getWidget('notice_message')->setValue('Bericht verwijderd');
            }
            else {
                if ($sm->getSaveKey() == true) {
                    
                    $this->form->getWidget('container-top')->getWidget('link_to_message')->setValue( $sm->getPublicLink() );
                }
                
                if ($sm->getSaveKey() == false) {
                    $this->form->getWidget('container-top')->getWidget('link_to_message')->setValue( 'Sleutel niet opgeslagen' );
                    
                    $this->form->getWidget('container-message')->removeWidget('plain_message');
                    $this->form->getWidget('container-enc-settings')->removeWidget('save_key');
                    
                    if ($sm->isAvailable()) {
                        $this->form->getWidget('container-message')->getWidget('notice_message')->setValue('Bericht gecodeerd opgeslagen');
                    }
                    else {
                        $this->form->getWidget('container-message')->getWidget('notice_message')->setValue('Bericht verwijderd');
                    }
                }
            }
        }
        
        $this->sm = $sm;
        $this->smlogs = $smservice->readMessageLog( $sm->getSecureMessageId() );
        
        return $this->render();
    }
    
    
    public function action_delete() {
        $id = (int)get_var('sm_id');
        
        
        $smservice = object_container_get( SecureMailService::class );
        $smservice->markDeleted( $id, remote_addr(), 'Marked deleted through admin' );
        
        report_user_message( t('Message marked as deleted') );
        
        redirect( '/?m=securemail&c=secureMessage&a=edit&sm_id='.$id );
    }

    public function action_purge() {
        $id = (int)get_var('sm_id');
        
        
        $smservice = object_container_get( SecureMailService::class );
        $smservice->deleteMessage( $id );
        
        report_user_message( t('Message deleted') );
        
        redirect( '/?m=securemail&c=secureMessage' );
    }
    
    
    public function action_show_link() {
        $smk = @$_SESSION['secure_message_key'];
        unset($_SESSION['secure_message_key']);
        
        if (!$smk || get_var('sm_id') != $smk['secure_message_id']) {
            $this->error = 'Bericht niet gevonden';
            return $this->render();
        }
        
        $smservice = object_container_get( SecureMailService::class );
        $sm = $smservice->readMessage( $smk['secure_message_id'] );
        
        $sm->setPassword( $smk['password'] );
        $this->sm = $sm;
        
        return $this->render();
    }
    
    
}



