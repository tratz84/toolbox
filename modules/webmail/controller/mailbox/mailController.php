<?php




use core\container\ActionContainer;
use core\controller\BaseController;
use core\exception\ObjectNotFoundException;
use core\forms\SelectField;
use webmail\WebmailSettings;
use webmail\form\EmailForm;
use webmail\mail\SolrMailActions;
use webmail\model\Connector;
use webmail\model\Email;
use webmail\service\ConnectorService;
use webmail\service\EmailService;
use webmail\solr\SolrMail;
use webmail\solr\SolrMailQuery;

class mailController extends BaseController {
   
    
    protected function getMail($id) {
        $smq = new SolrMailQuery();
        
        return $smq->readById($id);
    }
    
    /**
     * TODO: rename to 'mailactions_box' or something?
     * 
     */
    public function action_mailactions() {
        if (isset($this->emailId) == false) {
            print 'mailbox/mail::action_mailactions(), no emailId set';
            return;
        }
        
        $emailId = $this->emailId;
        
        // action buttons for e-mail
        $actionContainer = new ActionContainer('mail-actions', null);
        $actionContainer->setAttribute('data-email-id', $emailId);
        
        
        $f = get_data_file_safe('webmail/inbox', substr($emailId, strlen('/webmail/inbox')));
        if (!$f) {
            throw new ObjectNotFoundException('Mail not found');
        }
        
        
        // forward/reply buttons
        $actionContainer->addItem('mail-forward', '<button class="btn-forward-mail" onclick="forwardMail('.esc_json_attr($emailId).');"><span class="fa fa-forward"></span>Forward</button>');
        $actionContainer->addItem('mail-reply', '<button class="btn-reply-mail" onclick="replyMail('.esc_json_attr($emailId).');"><span class="fa fa-reply"></span>Reply</button>');
        
        
        $solrMail = SolrMailQuery::readStaticById($emailId);
        
        if ($solrMail == null) {
            $this->setTemplateFile( module_file('webmail', 'templates/mailbox/search/not-found.php') );
            return $this->render();
        }
        
        $mp = $solrMail->getProperties();
        if ($mp->getSeen() == false) {
            try {
                $sma = new SolrMailActions();
                $sma->markAsSeen($solrMail);
            } catch (\Exception|\Error $ex) { }
        }
        
        
        // move to folder
        if ($mp->getConnectorId()) {
            /** @var ConnectorService $connectorService */
            $connectorService = object_container_get(ConnectorService::class);
            /** @var Connector $connector */
            $connector = $connectorService->readConnector($mp->getConnectorId());
            
            $mapFolders = array();
            if ($connector) foreach($connector->getImapfolders() as $if) {
                $mapFolders[$if->getFolderName()] = $if->getFolderName();
            }
            
            $selectFolders = new SelectField('move_imap_folder', $mp->getFolder(), $mapFolders, null, ['add-unlisted' => true]);
            $selectFolders->setAttribute('onchange', 'moveMail('.json_encode($emailId).', this.value)');
            
            $actionContainer->addItem('move-mail-to-folder', $selectFolders->render());
        }
        
        // Action-state
        $mapActions = mapMailActions();
        $selectActions = new SelectField('set_action', $mp->getAction(), $mapActions);
        $selectActions->setAttribute('onchange', 'setMailAction('.json_encode($emailId).', this.value)');
        $actionContainer->addItem('set-mail-action', $selectActions->render());
        
        
        if ($mp->isJunk() == false) {
            $spam_onclick = "if (confirm('Are you sure to mark this mail as spam?')) markMailAsSpam(".json_encode($emailId).");";
            $actionContainer->addItem('mark-as-spam', '<button title="'.esc_attr(t('Mark as spam')).'" onclick="' . esc_attr($spam_onclick) . '"><span class="fa fa-flag mark-as-spam"></span></button>');
        }
        
        // delete-button
        $delete_onclick = "if (confirm('Are you sure to delete this mail?')) deleteMail(".json_encode($emailId).");";
        $actionContainer->addItem('delete-mail', '<button title="'.esc_attr(t('Delete mail')).'" onclick="' . esc_attr($delete_onclick) . '"><span class="fa fa-trash delete-mail"></span></button>');
        
        
        hook_eventbus_publish($actionContainer, 'webmail', 'mailbox-mailactions');
        
        $this->actionContainer = $actionContainer;
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    
    public function action_view() {
        /** @var SolrMail $mail */
        $mail = $this->getMail( get_var('id') );
        
        $this->id          = $mail->getId();
        
        $this->html        = $mail->getContentSafe();
        $this->date        = format_date($mail->getDate(), 'd-m-Y H:i:s');
        
        $this->attachments = $mail->getAttachments();
        
        $this->fromName    = $mail->getFromName();
        $this->fromEmail   = $mail->getFromEmail();
        
        $this->to          = $mail->getTo();
        $this->cc          = $mail->getCc();
        $this->bcc         = $mail->getBcc();
        $this->subject     = $mail->getSubject();
        
        hook_htmlscriptloader_enableGroup('webmail');
        
        $this->setDecoratorFile( module_file('base', 'templates/decorator/blank.php') );
        
        return $this->render();
    }
    
    
    public function action_attachment() {
        /** @var SolrMail $mail */
        $mail = $this->getMail( get_var('id') );
        
        $f = $mail->getAttachmentFile( get_var('no') );
        
        header('Content-type: ' . file_mime_type($f['filename']));
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
            $r = $ma->markAsSpam($mail);
            $ma->closeConnection();
            
            return $this->json([
                'success' => true,
                'folder' => isset($r['folder']) ? $r['folder'] : null
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
    
    public function action_delete_mail() {
        $smq = object_container_create(SolrMailQuery::class);
        
        try {
            $mail = $smq->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            
            $ma = new SolrMailActions();
            $ma->deleteMail($mail);
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
        
        $formData = array();
        
        
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
            $formData['identity_id'] = $foundIdentity->getIdentityId();
        }
        
        
        // set subject
        $subject = $mail->getSubject();
        if (stripos($subject, 're:') === false && stripos($subject, 'antwd:') === false) {
            $subject = 'Re: ' . $subject;
        }
        $formData['subject'] = $subject;
        
        
        // set to
        $formData['recipients'] = array();
        $formData['recipients'][] = array(
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
            
            $formData['recipients'][] = array(
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
            
            $formData['recipients'][] = array(
                'to_type' => 'Cc',
                'to_name' => $cc['name'],
                'to_email' => $cc['email']
            );
        }
        
        $formData['companyId'] = '';
        $formData['personId'] = '';
        
        // set content
        $webmailSettings = object_container_get(WebmailSettings::class);
        $text_content = $webmailSettings->getTemplateContentReplyMail();
        $formData['text_content'] = $text_content . '<br/><hr/>'.$mail->getContentSafe();
        
        // create form
        $form = new EmailForm();
        $form->addIdentities();
        $form->bind( $formData );
        $form->getWidget('status')->setValue(Email::STATUS_DRAFT);
        $form->getWidget('incoming')->setValue(false);
        $form->getWidget('solr_mail_id')->setValue($mail->getId());
        
        
        /** @var EmailService $emailService */
        $emailService = object_container_get(EmailService::class);
        $email = $emailService->saveEmail($form);
        
        report_user_message(t('E-mail created'));
        redirect('/?m=webmail&c=view&id='.$email->getEmailId());
    }
    
    public function action_forward() {
        $smq = object_container_create(SolrMailQuery::class);
        
        /** @var \webmail\solr\SolrMail $mail */
        $mail = $smq->readById( get_var('email_id') );
        
        if (!$mail) {
            throw new ObjectNotFoundException('Mail not found');
        }
        
        $formData = array();
        
        
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
            $formData['identity_id'] = $foundIdentity->getIdentityId();
        }
        
        
        // set subject
        $subject = $mail->getSubject();
        if (stripos($subject, 'fwd:') === false) {
            $subject = 'Fwd: ' . $subject;
        }
        $formData['subject'] = $subject;
        
        $formData['companyId'] = '';
        $formData['personId'] = '';
        
        // set content
        $webmailSettings = object_container_get(WebmailSettings::class);
        $text_content = $webmailSettings->getTemplateContentForwardMail();
        $formData['text_content'] = $text_content.'<br/><hr/>'.$mail->getContentSafe();
        
        
        $attachments = array();
        $attachmentsMeta = $mail->getAttachments();
        for($x=0; $x < count($attachmentsMeta); $x++) {
            $attachments[] = $mail->getAttachmentFile( $x );
        }
        
        // create form
        $form = new EmailForm();
        $form->bind( $formData );
        $form->getWidget('status')->setValue(Email::STATUS_DRAFT);
        $form->getWidget('incoming')->setValue(false);
        $form->getWidget('solr_mail_id')->setValue($mail->getId());
        

        /** @var EmailService $emailService */
        $emailService = object_container_get(EmailService::class);
        $email = $emailService->saveEmail($form, $attachments);
        
        report_user_message(t('E-mail created'));
        redirect('/?m=webmail&c=view&id='.$email->getEmailId());
    }
    
}

