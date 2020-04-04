<?php




use core\controller\BaseController;
use core\exception\ObjectNotFoundException;
use webmail\form\EmailForm;
use webmail\mail\SolrMailActions;
use webmail\service\ConnectorService;
use webmail\solr\SolrMail;
use webmail\solr\SolrMailQuery;
use webmail\service\EmailService;

class mailController extends BaseController {
   
    
    protected function getMail($id) {
        $smq = new SolrMailQuery();
        
        return $smq->readById($id);
    }
    
    
    public function action_view() {
        /** @var SolrMail $mail */
        $mail = $this->getMail( get_var('id') );
        
        $this->id          = $mail->getId();
        
        $this->html        = $mail->getContentSafe();
        $this->date        = $mail->getDate();
        
        $this->attachments = $mail->getAttachments();
        
        $this->fromName    = $mail->getFromName();
        $this->fromEmail   = $mail->getFromEmail();
        
        $this->to          = $mail->getTo();
        $this->cc          = $mail->getCc();
        $this->bcc         = $mail->getBcc();
        $this->subject     = $mail->getSubject();
        
        $this->setDecoratorFile( module_file('base', 'templates/decorator/blank.php') );
        
        return $this->render();
    }
    
    
    public function action_attachment() {
        /** @var SolrMail $mail */
        $mail = $this->getMail( get_var('id') );
        
        $f = $mail->getAttachmentFile( get_var('no') );
        
        header('Content-type: ' . $f['contentType']);
        header('Content-disposition: inline; filename="' . $f['filename'] .'"');
        print $f['content'];
    }
    
    
    
    public function action_move_mail() {
        $smq = object_container_create(SolrMailQuery::class);
        
        try {
            /** @var \webmail\solr\SolrMail $mail */
            $mail = $smq->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            
            /** @var \webmail\mail\MailProperties $mailProperties */
            $mailProperties = $mail->getProperties();
            
            $newFolder = get_var('target_folder');
            
            $connectorService = object_container_get(ConnectorService::class);
            
            /** @var \webmail\model\Connector $connector */
            $connector = $connectorService->readConnector( $mailProperties->getConnectorId() );
            
            $imapFolderId = null;
            if ($connector) {
                $ifs = $connector->getImapfolders();
                foreach($ifs as $if) {
                    if ($if->getFolderName() == $newFolder) {
                        $imapFolderId = $if->getConnectorImapFolderId();
                    }
                }
            }
            
            $ma = new SolrMailActions();
            if ($connector && $imapFolderId) {
                $ma->moveMail($connector, $mail, $imapFolderId);
            }
            else {
                $ma->updateSolrFolder($mail->getId(), $newFolder);
            }
            
            return $this->json([
                'success'   => true,
                'email_id'  => $mail->getId(),
                'newFolder' => $newFolder
            ]);
        } catch (\Exception $ex) {
            return $this->json([
                'error' => true,
                'message' => $ex->getMessage()
            ]);
        } catch (\Error $err) {
            return $this->json([
                'error' => true,
                'message' => $err->getMessage()
            ]);
        }
    }
    
    public function action_mail_action() {
        $smq = object_container_create(SolrMailQuery::class);
        
        try {
            /** @var \webmail\solr\SolrMail $mail */
            $mail = $smq->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            
            /** @var \webmail\mail\MailProperties $mailProperties */
            $mailProperties = $mail->getProperties();
            
            $mailProperties->setAction( get_var('action') );
            $mailProperties->save();
            
            $ma = new SolrMailActions();
            $ma->updateAction($mail->getId(), get_var('action'));
            
            
            return $this->json([
                'success'   => true,
                'email_id'  => $mail->getId(),
                'action' => get_var('action')
            ]);
        } catch (\Exception $ex) {
            return $this->json([
                'error' => true,
                'message' => $ex->getMessage()
            ]);
        } catch (\Error $err) {
            return $this->json([
                'error' => true,
                'message' => $err->getMessage()
            ]);
        }
    }
    
    
    public function action_mark_as_spam() {
        $smq = object_container_create(SolrMailQuery::class);
        
        try {
            $mail = $smq->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            
            $ma = new SolrMailActions();
            $ma->markAsSpam($mail);
            $ma->closeConnection();
            
            return $this->json([
                'success' => true
            ]);
        } catch (\Exception $ex) {
            return $this->json([
                'error' => true,
                'message' => $ex->getMessage()
            ]);
        } catch (\Error $err) {
            return $this->json([
                'error' => true,
                'message' => $err->getMessage()
            ]);
        }
    }

    public function action_mark_as_ham() {
        $smq = object_container_create(SolrMailQuery::class);
        
        try {
            $mail = $smq->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            
            $ma = new SolrMailActions();
            $ma->markAsHam($mail);
            $ma->closeConnection();
            
            return $this->json([
                'success' => true
            ]);
        } catch (\Exception $ex) {
            return $this->json([
                'error' => true,
                'message' => $ex->getMessage()
            ]);
        } catch (\Error $err) {
            return $this->json([
                'error' => true,
                'message' => $err->getMessage()
            ]);
        }
    }
    
    
    public function action_reply() {
        $smq = object_container_create(SolrMailQuery::class);
        
        /** @var \webmail\solr\SolrMail $mail */
        $mail = $smq->readById( get_var('email_id') );
        
        if (!$mail) {
            throw new ObjectNotFoundException('Mail not found');
        }
        
        $form = array();
        
        
        // lookup identity
        $emailService = object_container_get(EmailService::class);
        $identities = $emailService->readAllIdentities();
        $recipients = $mail->getRecipients();
        $foundIdentity = null;
        foreach($identities as $i) {
            foreach($recipients as $r) {
                if (strtolower($r['email']) == $i->getFromEmail()) {
                    $foundIdentity = $i;
                    break 2;
                }
            }
        }
        if ($foundIdentity) {
            $form['identity_id'] = $foundIdentity->getIdentityId();
        }
        
        
        // set subject
        $subject = $mail->getSubject();
        if (strpos($subject, 're:') === false && strpos($subject, 'antwd:') === false) {
            $subject = 'Re: ' . $subject;
        }
        $form['subject'] = $subject;
        
        
        // set to
        $form['recipients'] = array();
        $form['recipients'][] = array(
            'to_type' => 'To',
            'to_name' => $mail->getFromName(),
            'to_email' => $mail->getFromEmail()
        );
        
        foreach($mail->getTo() as $to) {
            // skip own addresses
            foreach($identities as $i) {
                if (strtolower($i->getFromEmail()) == strtolower($to['email'])) {
                    continue 2;
                }
            }
            
            $form['recipients'][] = array(
                'to_type' => 'To',
                'to_name' => $to['name'],
                'to_email' => $to['email']
            );
        }
        
        foreach($mail->getCc() as $cc) {
            // skip own addresses
            foreach($identities as $i) {
                if (strtolower($i->getFromEmail()) == strtolower($cc['email'])) {
                    continue 2;
                }
            }
            
            $form['recipients'][] = array(
                'to_type' => 'Cc',
                'to_name' => $cc['name'],
                'to_email' => $cc['email']
            );
        }
        
        $form['companyId'] = '';
        $form['personId'] = '';
        
        // set content
        $form['text_content'] = '<br/><br/><br/><hr/>'.$mail->getContentSafe();
        
        $_SESSION['webmail-form-data'] = $form;
        
        redirect('/?m=webmail&c=view&r=1');
    }
    
    public function action_forward() {
        
    }
    
}

