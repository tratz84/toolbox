<?php

namespace webmail\mail;

use core\exception\InvalidStateException;
use webmail\service\EmailService;
use core\Context;
use webmail\model\Email;
use core\exception\ResourceException;
use webmail\service\ConnectorService;
use webmail\solr\SolrMail;
use webmail\solr\SolrMailQuery;

class SendMail {
    
    protected $to = array();
    protected $cc = array();
    protected $bcc = array();
    
    protected $extraHeaders = array();
    
    protected $subject = '';
    protected $fromName = null;
    protected $fromEmail = null;
    
    protected $attachmentFiles = array();
    protected $attachmentDataFiles = array();
    
    protected $emailId = null;
    protected $identityId = null;
    
    protected $content = null;
    
    protected $error = null;
    
    public function __construct() {
        $ctx = object_container_get(Context::class);
        $this->setFromEmail( $ctx->getCompanyEmail() );
        
    }
    
    public function addTo($email, $name=null) {
        $this->to[] = array('name' => $name, 'email' => $email);
    }
    public function getTo() { return $this->to; }
    public function clearTo() { $this->to = array(); }
    
    public function addCc($email, $name=null) {
        $this->cc[] = array('name' => $name, 'email' => $email);
    }
    public function getCc() { return $this->to; }
    public function clearCc() { $this->to = array(); }
    
    public function addBcc($email, $name=null) {
        $this->bcc[] = array('name' => $name, 'email' => $email);
    }
    public function getBcc() { return $this->to; }
    public function clearBcc() { $this->to = array(); }
    
    public function addHeader($name, $val) {
        $this->extraHeaders[] = array(
            'name' => $name,
            'value' => $val
        );
    }
    
    public function setEmailId($id) { $this->emailId = $id; }
    public function getEmailId() { return $this->emailId; }
    
    public function setIdentityId($id) { $this->identityId = $id; }
    public function getIdentityId() { return $this->identityId; }
    
    public function getFromName() { return $this->fromName; }
    public function setFromName($n) { $this->fromName = $n; }
    
    public function getFromEmail() { return $this->fromEmail; }
    public function setFromEmail($e) { $this->fromEmail = $e; }
    
    public function getSubject() { return $this->subject; }
    public function setSubject($s) { $this->subject = $s; }
    
    public function setContent($c) { $this->content = $c; }
    public function getContent() { return $this->content; }
    
    public function getError() { return $this->error; }
    
    
    public function addAttachmentFile($file, $filename=null) {
        $this->attachmentFiles[] = array(
            'file' => $file,
            'filename' => $filename
        );
    }
    
    public function addAttachmentDataFile($data, $filename) {
        $this->attachmentDataFiles[] = array(
            'data' => $data,
            'filename' => $filename
        );
    }
    
    public function buildMessage() {
        $message = new \Swift_Message( $this->getSubject() );
        
        foreach($this->extraHeaders as $h) {
            $message->getHeaders()->addTextHeader( $h['name'], $h['value'] );
        }
        
        $message->setFrom(array($this->getFromEmail() => $this->getFromName()));
        
        foreach($this->to as $m) {
            $message->addTo($m['email'], $m['name']);
        }
        foreach($this->cc as $m) {
            $message->addCc($m['email'], $m['name']);
        }
        foreach($this->bcc as $m) {
            $message->addBcc($m['email'], $m['name']);
        }
        
        $message->setBody($this->getContent(), 'text/html', 'UTF-8');
        
        foreach($this->attachmentFiles as $f) {
            $data = file_get_contents($f['file']);
            
            if ($data === false) {
                throw new InvalidStateException('Attachment not found');
            }
            
            if (!$f['filename'])
                $f['filename'] = basename($f['file']);
                
                $att = new \Swift_Attachment($data, $f['filename']);
                $message->attach($att);
        }
        
        foreach($this->attachmentDataFiles as $f) {
            $att = new \Swift_Attachment($f['data'], $f['filename']);
            $message->attach($att);
        }
        
        return $message;
    }
    
    
    public function send() {
        $emailService = object_container_get(EmailService::class);
        $settings = $emailService->getMailServerSettings();
        
        if ($settings['server_type'] == 'local') {
            // hmz..
            if (defined('SMTP_HOST') && SMTP_HOST) {
                $transport = new \Swift_SmtpTransport(SMTP_HOST, SMTP_PORT);
                if (defined('SMTP_USERNAME') && defined('SMTP_PASSWORD') && SMTP_USERNAME && SMTP_PASSWORD) {
                    $transport->setUsername(SMTP_USERNAME);
                    $transport->setPassword(SMTP_PASSWORD);
                }
            } else {
                // unix? =>K use sendmail
                if (file_exists('/usr/sbin/sendmail')) {
                    $transport = new \Swift_SendmailTransport();
                } else {
                    throw new ResourceException('No mail transport configured');
                }
            }
        } else {
            $transport = new \Swift_SmtpTransport($settings['mail_hostname'], $settings['mail_port']);
            if ($settings['mail_username'] && $settings['mail_password']) {
                $transport->setUsername($settings['mail_username']);
                $transport->setPassword($settings['mail_password']);
            }
            
        }
        
        $message = $this->buildMessage();
        
        $mailer = new \Swift_Mailer($transport);
        
        $ctx = object_container_get(Context::class);
        if ($ctx->getContextName() == 'demo') {
            // don't send mail in demo-environment
            return true;
        } else {
            try {
                $r = $mailer->send( $message );
            } catch (\Exception $ex) {
                $this->error = $ex->getMessage();
                return false;
            }
            
            // mail sent & identity is set? => try to save mail on imap server (if configured)
            if ($r && $this->getIdentityId()) {
                // call SolrMailActions::saveSendMail()
                // might return false if imap is not configured
                $solrMailActions = object_container_get(SolrMailActions::class);
                $solrMailActions->saveSendMail( $this );
            }
            
            
            
            return $r;
        }
    }
    
    
    public static function createMail(Email $email) {
        $ctx = object_container_get(Context::class);
        
        $sm = new SendMail();
        $sm->setEmailId($email->getEmailId());
        $sm->setIdentityId( $email->getIdentityId() );
        $sm->setSubject($email->getSubject());
        $sm->setFromName($email->getFromName());
        $sm->setFromEmail($email->getFromEmail());
        
        // add In-Reply-To header if available
        if ($email->getSolrMailId()) {
            $solrMail = SolrMailQuery::readStaticById( $email->getSolrMailId() );
            if ($solrMail && $solrMail->getEmlMessageId())
                $sm->addHeader('In-Reply-To', $solrMail->getEmlMessageId());
        }
        
        
        foreach($email->getRecipients() as $r) {
            if (strtolower($r->getToType()) == 'to' || $r->getToType() == null) {
                $sm->addTo($r->getToEmail(), $r->getToName());
            } else if (strtolower($r->getToType()) == 'cc') {
                $sm->addCc($r->getToEmail(), $r->getToName());
            } else if (strtolower($r->getToType()) == 'bcc') {
                $sm->addBcc($r->getToEmail(), $r->getToName());
            }
        }
        $sm->setContent($email->getTextContent());
        
        foreach($email->getFiles() as $f) {
            $full_path = $ctx->getDataDir() . '/' . $f->getPath();
            $data = file_get_contents($full_path);
            
            if ($data === false) {
                throw new InvalidStateException('Attachment not found');
            }
            
            $sm->addAttachmentDataFile($data, $f->getFilename());
        }
        
        return $sm;
    }
    
    
    
}
