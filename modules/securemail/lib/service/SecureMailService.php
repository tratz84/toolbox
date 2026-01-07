<?php


namespace securemail\service;


use base\util\ActivityUtil;
use core\db\DatabaseHandler;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use securemail\form\SecureMessageForm;
use securemail\model\SecureMessage;
use securemail\model\SecureMessageDAO;
use securemail\model\SecureMessageLogDAO;
use securemail\model\SecureMessageLog;
use core\exception\ObjectNotFoundException;

class SecureMailService extends ServiceBase {
    
    
    public function searchMessage($start, $limit, $opts = array()) {
        $smdao = new SecureMessageDAO();
        
        $cursor = $smdao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('secure_message_id'
            , 'ttl_type'
            , 'ttl_expires_on'
            , 'person_id'
            , 'company_id'
            , 'firstname'
            , 'lastname'
            , 'company_name'
            , 'short_description'
//             , 'password'
            , 'deleted'
            , 'edited'
            , 'created'));
        
        return $r;
    }
    
    
    
    public function readMessage( $id ) {
        $smdao = new SecureMessageDAO();
        
        $sm = $smdao->read((int)$id);
        
        if (!$sm) {
            return null;
        }
        
        if ($sm->getPassword()) {
            $sm->setPlainMessage( $sm->getDecryptedMessage() );
            $sm->setSaveKey( true );
        }
        
        return $sm;
    }
    
    public function readMessageLog( $secureMessageId ) {
        $smldao = new SecureMessageLogDAO();
        return $smldao->readByMessage($secureMessageId);
    }
    
    
    public function saveMessage( SecureMessageForm $form ) {
        
        $id = (int)$form->getWidgetValue('secure_message_id');
        
        $old_secmsg = null;
        $secmsg = null;
        if ($id) {
            $old_secmsg = $this->readMessage($id);
            $secmsg = $this->readMessage($id);
        }
        else {
            $secmsg = new SecureMessage();
        }
        
        
        $form->fill($secmsg, ['customer_id', 'short_description', 'plain_message', 'ttl_type', 'ttl_expires_on']);
        
        $r = null;
        if ($id && trim($form->getWidgetValue('plain_message')) == '') {
            
        }
        else {
            $msg = $secmsg->getPlainMessage();
            
            if ($id && $form->getWidgetValue('save_key') && $secmsg->getPassword()) {
                $tag = null;
                $encrypted_msg = openssl_encrypt($msg, $secmsg->getEncryptionAlgo(), $secmsg->getPassword(), 0, base64_decode($secmsg->getEncryptionIv()), $tag);
                $secmsg->setEncryptedMessage($encrypted_msg);
            }
            else {
                $r = sm_encrypt_message($msg);
                
                $secmsg->setEncryptionAlgo($r['cipher']);
                $secmsg->setEncryptionIv( base64_encode( $r['iv'] ) );
                $secmsg->setEncryptedMessage( $r['ciphertext'] );
                if ($form->getWidgetValue('save_key')) {
                    $secmsg->setPassword( $r['password'] );
                }
                else {
                    $secmsg->setPassword( null );
                }
                $secmsg->setPasswordHash( md5( $r['password'] ) );
            }
        }
        
        $secmsg->save();
        
        // encryption result set? => always set password, so we can show
        // user link to public request page
        if ( $r != null ) {
            $secmsg->setPassword( $r['password'] );
        }
        
        return $secmsg;
    }
    
    
    public function markDeleted( $secureMessageId, $ip, $logMessage=null ) {
        $sm = $this->readMessage($secureMessageId);
        
        if (valid_datetime($sm->getDeleted())) {
            // could return here.. on the other hand, why not log it?
        }
        
        
        $con = DatabaseHandler::getConnection('default');
        $con->query("update securemail__secure_message set deleted=now(), encrypted_message=null, password=null where secure_message_id = ?", array($sm->getSecureMessageId()));
        
        $sml = new SecureMessageLog();
        $sml->setSecureMessageId( $sm->getSecureMessageId() );
        if ($logMessage == null) {
            $sml->setLogMessage( 'Message marked as deleted' );
        }
        else {
            $sml->setLogMessage($logMessage);
        }
        $sml->setIp( $ip );
        $sml->save();
    }
    
    
    
    public function deleteMessage( $secureMessageId ) {
        $sm = $this->readMessage( $secureMessageId );
        
        $sm->delete();
        
        ActivityUtil::logActivity(
            $sm->getCompanyId()
            , $sm->getPersonId()
            , 'securemail__secure_message'
            , $sm->getSecureMessageId()
            , 'secure-message-deleted'
            , 'Beveiligd bericht handmatig verwijderd: '.$sm->getSecureMessageId()
            , 'Korte omschrijving: ' . $sm->getShortDescription()
        );
    }
    
    
    public function deleteExpired() {
        
        $smdao = new SecureMessageDAO();
        $sms = $smdao->readOpenExpired();
        
        foreach($sms as $sm) {
            $this->markDeleted($sm->getSecureMessageId(), null, 'Auto-expiry');
        }
        
    }
    
    
    
}




