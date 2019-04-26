<?php


use base\service\CustomerService;
use core\controller\BaseController;
use invoice\service\OfferService;
use signrequest\form\SignRequestForm;
use webmail\service\EmailTemplateService;
use signrequest\service\SignRequestService;
use core\exception\ObjectNotFoundException;
use signrequest\model\Message;
use signrequest\model\MessageSigner;
use core\exception\InvalidStateException;
use core\exception\RemoteApiException;

class offerController extends BaseController {
    
    
    public function action_create() {
        
        $offerService = $this->oc->get(OfferService::class);
        $offer = $offerService->readOffer((int)$_REQUEST['offer_id']);
        if (!$offer) {
            throw new ObjectNotFoundException('Offer not found');
        }
        
        $customerService = $this->oc->get(CustomerService::class);
        $customer = $customerService->readCustomerAuto($offer->getCompanyId(), $offer->getPersonId());
        if (!$customer) {
            throw new ObjectNotFoundException('Customer not found');
        }
        
        // build message
        $msg = new Message();
        $msg->setRefObject('offer');
        $msg->setRefId($offer->getOfferId());
        $msg->setSent(false);
        
        // set default values
        $emailTemplateService = $this->oc->get(EmailTemplateService::class);
        $tpl = $emailTemplateService->readByTemplateCode('SIGNREQUEST_TEMPLATE');
        if ($tpl) {
            $vars = array();
            
            if ($customer->getType() == 'company')
                $vars['naam'] = $customer->getField('contact_person');
            if ($customer->getType() == 'person')
                $vars['naam'] = $customer->getPersonName();
            
            $msg->setMessage( $tpl->render($vars) );
        }
        
        $signers = array();
        $emails = $customer->getEmailList();
        foreach($emails as $e) {
            $ms = new MessageSigner();
            $ms->setSignerEmail( $e->getEmailAddress() );
            $signers[] = $ms;
        }
        $msg->setSigners( $signers );
        
        // save
        $signRequestService = $this->oc->get(SignRequestService::class);
        $messageId = $signRequestService->createSignRequest( $msg );
        
        // redirect
        redirect('/?m=signrequest&c=offer&a=edit&id=' . $messageId);
    }
    
    
    public function action_edit() {
        
        $signRequestService = $this->oc->get(SignRequestService::class);
        $message = $signRequestService->readMessage( get_var('id') );
        
        if ($message->getSent()) {
            throw new InvalidStateException('Message already sent');
        }
        
        $offerService = $this->oc->get(OfferService::class);
        $this->offer = $offerService->readOffer((int)$message->getRefId());
        
        $this->form = new SignRequestForm();
        $this->form->bind( $message );
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if (has_file('files')) {
                // add file
                $signRequestService->addFileByPath( $message->getMessageId(), $_FILES['files']['name'],  $_FILES['files']['tmp_name'] );
            } else if (get_var('delete_files')) {
                $messageFileId = get_var('delete_files');
                $signRequestService->deleteFile($message->getMessageId(), $messageFileId);
            }
            
            $signRequestService->saveSignRequest( $this->form );
            
            // send signrequest
            if (get_var('send') && $this->form->validate()) {
                $offerPdf = $offerService->createPdf( $this->offer->getOfferId() );
                
                $files = array();
                $files['offerte-'.$this->offer->getOfferNumberText().'.pdf'] = $offerPdf->Output('S');
                
                foreach($message->getFiles() as $f) {
                    $contents = file_get_contents( get_data_file($f->getPath()) );
                    if ($contents) {
                        $files[$f->getName()] = $contents;
                    }
                }
                
                $signRequestService = $this->oc->get(SignRequestService::class);
                
                $message = $signRequestService->saveSignRequest( $this->form );
                try {
                    $message = $signRequestService->sendSignRequest( $message->getMessageId(), $files );
                } catch (RemoteApiException $ex) {
                    return $this->renderError($ex->getMessage());
                }
                
                $back_url = '/?m=invoice&c=offer&a=edit&id=' . $this->offer->getOfferId();
                
                redirect('/?m=signrequest&c=status&a=view&id='.$message->getMessageId().'&back_url='.urlencode($back_url));
            }
        }
        
        // post & fallen through? => rebind, files-property might have changed
        if (is_post()) {
            $message = $signRequestService->readMessage( get_var('id') );
            $this->form->bind( $message );
            $this->form->bind( $_REQUEST );
        }
        
        return $this->render();
    }
    
}

