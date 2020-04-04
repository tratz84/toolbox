<?php


use core\controller\BaseController;
use core\exception\ObjectNotFoundException;
use core\forms\HtmlField;
use webmail\form\EmailForm;
use webmail\mail\SendMail;
use webmail\mail\SolrMailActions;
use webmail\model\Email;
use webmail\model\EmailTo;
use webmail\service\EmailService;
use webmail\solr\SolrMailQuery;

class viewController extends BaseController {
    
    public function init() {
        $this->addTitle(t('E-mail'));
    }
    
    public function action_index() {
        
        $emailService = $this->oc->get(EmailService::class);
        
        if (get_var('id')) {
            $email = $emailService->readEmail($_REQUEST['id']);
        } else {
            $email = new Email();
            $email->setStatus(Email::STATUS_DRAFT);
            $email->setIncoming(false);
        }
        
        if ($email === null) {
            throw new ObjectNotFoundException('Requested e-mail not found');
        }
        
        $this->addTitle(t('Subject') . ': ' . $email->getSubject());

        $this->form = new EmailForm();
        $this->form->bind( $email );
        
        if (is_post()) {
            if (has_file('files')) {
                // add file
                $emailService->addFileByPath( $email->getEmailId(), $_FILES['files']['name'],  $_FILES['files']['tmp_name'] );
            } else if (get_var('delete_files')) {
                $emailFileId = get_var('delete_files');
                $emailService->deleteFile($email->getEmailId(), $emailFileId);
            }
            
            $this->form->bind( $_REQUEST );
            
            $email = $emailService->saveEmail($this->form);
            
            if (get_var('sendmail')) {
                redirect('/?m=webmail&c=view&a=send&id=' . $email->getEmailId());
            } else {
                report_user_message('Wijzigingen opgeslagen');
                
                redirect('/?m=webmail&c=view&id=' . $email->getEmailId());
            }
        }
        
        if (hasCapability('webmail', 'send-mail') == false) {
            report_user_message('Alleen toegang tot aanmaken van Concept-berichten (niet versturen)');
        }
        
        
        if (count($email->getRecipients()) == 0) {
            $email->setRecipients( array( new EmailTo() ));
        }
        $this->form->bind( $email );
        
        if (is_get() && get_var('r')) {
            //             var_export($_SESSION['webmail-form-data']);exit;
            $this->form->bind( $_SESSION['webmail-form-data'] );
        }
        
        
        
        if ($email->isNew()) {
            $this->form->addWidget(new HtmlField('lblNew', t('New e-mail'), t('Id')));
            $this->form->getWidget('lblNew')->setPrio(5);
            $this->form->removeWidget('email_id');
        }
        
        $this->emailStatus = $email->getStatus();
        
        $this->render();
    }
    
    
    public function action_file() {
        $emailService = $this->oc->get(EmailService::class);
        
        $emailFile = $emailService->readFile($_REQUEST['id']);
        
        if ($emailFile === null) {
            throw new ObjectNotFoundException('Requested file not found');
        }
        
        $path = $this->ctx->getDataDir() . '/' . $emailFile->getPath();
        
        if (file_exists($path) == false) {
            die('file not found');
        }
        
        if (function_exists('mime_content_type')) {
            $type = mime_content_type($path);
        } else {
            $type = 'application/octet-stream';
        }
        
        header('Content-type: '.$type);
        header('Content-Disposition: inline; filename="'.$emailFile->getFilename().'"');
        
        readfile($path);
    }
    
    
    public function action_send() {
        $emailService = $this->oc->get(EmailService::class);
        
        /** @var Email $email */
        $email = $emailService->readEmail($_REQUEST['id']);
        
        if ($email === null) {
            throw new ObjectNotFoundException('Requested e-mail not found');
        }
        
        
        $form = new EmailForm();
        $form->bind( $email );
        if (!$form->validate()) {
            $errors = $form->getErrorList();
            foreach($errors as $e) {
                report_user_error($e);
            }
            // shouldn't happen
            if (count($errors) == 0) {
                report_user_error('Er is een onbekende fout opgetreden, neem contact op met technisch beheer');
            }
            
            redirect('/?m=webmail&c=view&id=' . $email->getEmailId());
        }
        
        
        // send mail
        $sm = SendMail::createMail($email);
        if ($sm->send() == false) {
            // redirect back
            report_user_error('Sending mail failed');
            redirect('/?m=webmail&c=view&id=' . $email->getEmailId());
        }
        
        // mark mail as sent
        $emailService->markMailAsSent( $email->getEmailId() );
        
        // mark mail as sent on imap-server/solr
        if ($email->getSolrMailId()) {
            try {
                $smq = new SolrMailQuery();
                $solrMail = $smq->readById($email->getSolrMailId());
                
                if ($solrMail) {
                    $sma = new SolrMailActions();
                    $sma->markAsAnswered($solrMail);
                }
            } catch (\Exception|\Error $ex) {
                // mja
            }
        }
        
        
        // redirect to overview
        redirect('/?m=webmail&c=email');
    }
    
    
}

