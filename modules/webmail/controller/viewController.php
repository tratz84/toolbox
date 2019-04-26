<?php


use core\controller\BaseController;
use webmail\service\EmailService;
use core\exception\ObjectNotFoundException;
use webmail\form\EmailForm;
use webmail\form\EmailRecipientLineWidget;
use webmail\model\EmailTo;
use core\exception\InvalidStateException;

class viewController extends BaseController {
    
    
    public function action_index() {
        
        $emailService = $this->oc->get(EmailService::class);
        
        $email = $emailService->readEmail($_REQUEST['id']);
        
        if ($email === null) {
            throw new ObjectNotFoundException('Requested e-mail not found');
        }

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
            
            $emailService->saveEmail($this->form);
            
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
        $transport = new Swift_SmtpTransport(SMTP_HOST, SMTP_PORT);
        if (defined('SMTP_USERNAME') && defined('SMTP_PASSWORD') && SMTP_USERNAME && SMTP_PASSWORD) {
            $transport->setUsername(SMTP_USERNAME);
            $transport->setPassword(SMTP_PASSWORD);
        }
        
        
        $message = new Swift_Message( $email->getSubject() );
        $message->setFrom(array($email->getFromEmail() => $email->getFromName()));
        foreach($email->getRecipients() as $r) {
            if (strtolower($r->getToType()) == 'to') {
                $message->addTo($r->getToEmail(), $r->getToName());
            } else if (strtolower($r->getToType()) == 'cc') {
                $message->addCc($r->getToEmail(), $r->getToName());
            } else if (strtolower($r->getToType()) == 'bcc') {
                $message->addBcc($r->getToEmail(), $r->getToName());
            }
        }
        $message->setBody($email->getTextContent(), 'text/html', 'UTF-8');
        
        foreach($email->getFiles() as $f) {
            $full_path = $this->ctx->getDataDir() . '/' . $f->getPath();
            $data = file_get_contents($full_path);
            
            if ($data === false) {
                throw new InvalidStateException('Attachment not found');
            }
            
            $att = new Swift_Attachment($data, $f->getFilename());
            $message->attach($att);
        }
        
        
        $mailer = new Swift_Mailer($transport);
        
        if ($this->ctx->getContextName() == 'demo') {
            // don't send mail in demo-environment
        } else {
            $r = $mailer->send( $message );
        }
        
        
        // mark mail as sent
        $emailService->markMailAsSent( $email->getEmailId() );
        
        
        // redirect to overview
        redirect('/?m=webmail&c=email');
    }
    
    
}

