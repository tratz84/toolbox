<?php

namespace signrequest\service;

use base\util\ActivityUtil;
use core\Context;
use core\ObjectContainer;
use core\exception\FileException;
use core\service\ServiceBase;
use invoice\service\OfferService;
use signrequest\api\SignRequestApi;
use signrequest\form\SignRequestForm;
use signrequest\model\Message;
use signrequest\model\MessageDAO;
use signrequest\model\MessageFile;
use signrequest\model\MessageSignerDAO;
use webmail\service\EmailService;
use signrequest\model\MessageFileDAO;
use core\exception\RemoteApiException;

class SignRequestService extends ServiceBase {
    
    
    public function getCountByRef($refObject, $refId) {
        $mDao = new MessageDAO();
        
        return $mDao->getCountByRef($refObject, $refId);
    }
    
    public function getSentCountByRef($refObject, $refId) {
        $mDao = new MessageDAO();
        
        return $mDao->getSentCountByRef($refObject, $refId);
    }
    
    
    public function readMessage($messageId) {
        $mDao = new MessageDAO();
        $message = $mDao->read($messageId);
        
        
        $msDao = new MessageSignerDAO();
        $signers = $msDao->readByMessage($messageId);
        
        $message->setSigners($signers);
        
        $mfDao = new MessageFileDAO();
        $files = $mfDao->readByMessage($messageId);
        $message->setFiles( $files );
        
        return $message;
    }
    
    
    public function createSignRequest(Message $sr) {
        $sr->save();
        
        $msDao = new MessageSignerDAO();
        $signers = $sr->getSigners();
        $msDao->mergeFormListMTO1('message_id', $sr->getMessageId(), $signers);
        
        
        $offerAttachments = list_data_files('attachments/offer/');
        foreach($offerAttachments as $oaFilename) {
            $path = get_data_file('attachments/offer/'.$oaFilename);
            
            $messageFileId = $this->addFileByPath($sr->getMessageId(), basename($path), $path);
        }
        
        
        return $sr->getMessageId();
    }
    
    public function deleteFile($messageId, $messageFileId) {
        $mfDao = new MessageFileDAO();
        $mf = $mfDao->readFile($messageFileId, $messageId);
        
        if (!$mf)
            return false;
        
        $mf->delete();
        
        $f = get_data_file( $mf->getPath() );
        
        unlink($f);
    }
    
    /**
     * 
     * @param SignRequestForm $f
     */
    public function saveSignRequest(SignRequestForm $f) {
        $oc = ObjectContainer::getInstance();
        
        $message = new Message();
        // bind object (just one field for now)
        $f->fill($message, array('message_id', 'message', 'ref_object', 'ref_id'));

        // fetch identity
        $identity_id = $f->getWidget('identity_id')->getValue();
        $emailService = $oc->get(EmailService::class);
        $identity = $emailService->readIdentity($identity_id);
        
        // set from
        $message->setFromName( $identity->getFromName() );
        $message->setFromEmail( $identity->getFromEmail() );
        $message->save();
        
        // set signers
        $msDao = new MessageSignerDAO();
        $arrSigners = $f->getWidget('signers')->getObjects();
        $msDao->mergeFormListMTO1('message_id', $message->getMessageId(), $arrSigners);
        
        
        $offerService = $oc->get(OfferService::class);
        $offer = $offerService->readOffer( $f->getWidget('ref_id')->getValue() );
        
        ActivityUtil::logActivity($offer->getCompanyId(), $offer->getPersonId(), 'offer', $offer->getOfferId(), 'signrequest', 'SignRequest verstuurt voor offerte ' . $offer->getOfferNumberText());
        
        return $message;
    }
    
    /**
      * @param array $files - key-value array with filenames + data,
     *                  array(
     *                      'file1.pdf' => '.....',
     *                     )
     */
    public function sendSignRequest($messageId, $files=array()) {
        
        $message = $this->readMessage($messageId);
        
        $sra = new SignRequestApi();
        
        $signers = array();
        foreach($message->getSigners() as $s) {
            $signers[] = $s->getSignerEmail();
        }
        
        $r = $sra->createDocument( $files );
        
        if ($r && is_object($r) && isset($r->url)) {
            $r = $sra->signRequest($r->url, $message->getFromEmail(), $message->getMessage(), $signers);
        } else {
            throw new RemoteApiException($r->detail);
        }
        
        $message->setDocumentsResponse( $sra->getResponseCreateDocument() );
        $message->setSignrequestsResponse( $sra->getResponseSignRequest() );
        $message->setSent( true );
        $message->save();
        
        return $message;
    }
    
    
    public function addFileByPath($messageId, $filename, $tmpPath) {
        $data = file_get_contents($tmpPath);
        
        return $this->addFile($messageId, $filename, $data);
    }
    
    public function addFile($messageId, $filename, $data) {
        $ctx = Context::getInstance();
        $datadir = $ctx->getDataDir();
        
        $mf = new MessageFile();
        $mf->setMessageId($messageId);
        $mf->setFilename($filename);
        $mf->save();
        
        if (is_dir($datadir.'/signrequestfiles') == false) {
            if (mkdir( $datadir.'/signrequestfiles' ) == false) {
                throw new FileException('Unable to create signrequest-file directory');
            }
        }
        
        $path = 'signrequestfiles/' . $mf->getMessageFileId() . '-' . $filename;
        if (file_put_contents($datadir.'/'.$path, $data) === false) {
            throw new FileException('Unable to write file');
        }
        
        $mf->setPath($path);
        $mf->save();
        
        return $mf->getMessageFileId();
    }
    
    
}

