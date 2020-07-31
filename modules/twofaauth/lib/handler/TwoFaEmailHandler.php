<?php


namespace twofaauth\handler;

use core\template\DefaultTemplate;
use webmail\model\EmailTo;
use webmail\service\EmailService;

class TwoFaEmailHandler {

    
    public function __construct() {
        
    }
    
    
    public function execute() {
        $user = ctx()->getUser();
        
        $tplEmail = new DefaultTemplate( module_file('twofaauth', 'templates/auth/email.php') );
        $tplEmail->setVar('masked_email', \mask_email($user->getEmail()));
        
        if (validate_email( $user->getEmail() ) == false) {
            $tplEmail->setVar('fatal_error', t('User has no valid e-mail configured. Unable to proceed'));
        }
        
        
        $tplDecorator = new DefaultTemplate( module_file('base', 'templates/decorator/blank.php') );
        $tplDecorator->setVar('context', ctx());
        $tplDecorator->setVar('pageTitle', ['Two factor authentication']);
        $tplDecorator->setVar('content', $tplEmail->getTemplate());
        $tplDecorator->showTemplate();
        exit;
    }
    
    public function sendMail() {
        
        $emailService = $this->oc->get(EmailService::class);
        $identity = $emailService->readSystemMessagesIdentity();
        
        
        $e = new \webmail\model\Email();
        $e->setStatus(\webmail\model\Email::STATUS_DRAFT);
        if ($identity) {
            $e->setIdentityId($identity->getIdentityId());
            $e->setFromName($identity->getFromName());
            $e->setFromEmail($identity->getFromEmail());
        }
        $e->setUserId($this->ctx->getUser()->getUserId());
        $subject = apply_html_vars($template->getSubject(), $vars);
        $e->setSubject($subject);
        $e->setTextContent($html);
        $e->setIncoming(false);
        
        
        $emailAddresses = $invoice->getCustomer()->getEmailList();
        if (count($emailAddresses) > 0) {
            $et = new EmailTo();
            $et->setToName( $vars['naam'] );
            $et->setToEmail( $emailAddresses[0]->getEmailAddress() );
            
            $e->addRecipient($et);
        }
        
        
        
        $emailService->createDraft($e, $files);
        
    }
    
    
}

