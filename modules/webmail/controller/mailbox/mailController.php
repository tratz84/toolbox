<?php




use core\container\ActionContainer;
use core\controller\BaseController;
use core\exception\ObjectNotFoundException;
use core\forms\SelectField;
use webmail\WebmailSettings;
use webmail\form\EmailForm;
use webmail\mail\EmlViewer;
use webmail\mail\MailProperties;
use webmail\mail\action\MailActionsBase;
use webmail\model\Connector;
use webmail\model\Email;
use webmail\search\MailSearchBase;
use webmail\service\ConnectorService;
use webmail\service\EmailService;
use webmail\solr\SolrMail;
use webmail\solr\SolrMailQuery;
use webmail\mail\render\MailRenderBase;
use core\exception\InvalidStateException;
use core\exception\LockException;

class mailController extends BaseController {
   
    
    /**
     * getMail()
     * 
     * @return \webmail\mail\render\MailRenderBase
     */
    protected function getMail($id) {
        $ms = MailSearchBase::getInstance();
        
        return $ms->readById( $id );
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
        
        $ms = MailSearchBase::getInstance();
        $mail = $ms->readById( $emailId );
        
        if ($mail == null) {
            $this->setTemplateFile( module_file('webmail', 'templates/mailbox/search/not-found.php') );
            return $this->render();
        }
        
        $mp = new MailProperties( $emailId );
        $mp->load();
        
        
        // TODO
//         $mp = $solrMail->getProperties();
        $ma = MailActionsBase::getInstance();
        if ($mp->getSeen() == false) {
            try {
                $ma = MailActionsBase::getInstance();
                $ma->markAsSeen( $mail );
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
        
        if (get_var('embedded_view')) {
            $openMailWindow_onclick =" openMailWindow( " . json_encode( $emailId ) . "); ";
            $actionContainer->addItem('open-mail-window', '<button onclick="' . esc_attr($openMailWindow_onclick) . '"><span class="fa fa-external-link open-mail-window"></span></button>');
        }
        
        
        hook_eventbus_publish($actionContainer, 'webmail', 'mailbox-mailactions');
        
        $this->actionContainer = $actionContainer;
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    
    public function action_view() {
        /** @var MailRenderBase $mail */
        $mail = $this->getMail( get_var('id') );
        
        if (!$mail) {
            throw new ObjectNotFoundException( 'Mail not found' );
        }
        
        @$mail->parseMail();
        $emlviewer = $mail->getEmlViewer();
        
        $this->id          = $mail->getId();
        
        $this->html        = $mail->getContentSafe();
        $this->date        = format_date($mail->getDate(), 'd-m-Y H:i:s');
        
        $this->attachments = $emlviewer->getAttachments();
        
        $this->fromName    = $emlviewer->getFromName();
        $this->fromEmail   = $emlviewer->getFromEmail();
        
        $this->to          = $emlviewer->getTo();
        $this->cc          = $emlviewer->getCc();
        $this->bcc         = $emlviewer->getBcc();
        $this->subject     = $emlviewer->getSubject();
        
        hook_htmlscriptloader_enableGroup('webmail');

        $this->attachmentsRendered = array();
        
        $parser = $emlviewer->getParser();
        $atts = $parser->getAttachments();
        for($x=0; $x < count($atts); $x++) {
            $att = $mail->getAttachmentFile( $x );
            
            if (isset($att['contentType']) && $att['contentType'] == 'text/calendar') {
                $obj = null;
                try {
                    $obj = \Sabre\VObject\Reader::read( $att['content'] );
                } catch (\Exception $ex) { }
                
                // parse failed? => skip
                if (!$obj) {
                    continue;
                }
                
                // TODO: get events
                $it = $obj->getIterator();
                do {
                    $comp = $it->current();
                    
                    if ($comp->name == 'VCALENDAR') {
                        /** @var \Sabre\VObject\Component\VCalendar $comp */
                        
                        $html = '';
                        $txt = '';
                        foreach($comp->VEVENT as $evt) {
                            foreach($evt->children() as $c) {
                                
                                $txtName = ucfirst(strtolower($c->name));
                                
                                if (is_a($c, \Sabre\VObject\Property\ICalendar\CalAddress::class)) {
                                    /** @var \Sabre\VObject\Property\ICalendar\CalAddress $c */
                                    if (strpos($c->getValue(), 'mailto:') === 0) {
                                        $html .= $txtName . ': '.'<a href="'.esc_attr($c->getValue()).'">'.esc_html(substr($c->getValue(), 7)).'</a>'. "<br/>" . PHP_EOL;
                                    }
                                    else {
                                        $html .= $txtName . ': '.esc_html($c->getValue()) . "<br/>" . PHP_EOL;
                                    }
                                }
                                if (is_a($c, \Sabre\VObject\Property\FlatText::class)) {
                                    /** @var \Sabre\VObject\Property\FlatText $c */
                                    
                                    if (substr_count($c->getValue(), "\n") > 1) {
                                        $txt .= '<b>'.$txtName . '</b><br/>'.nl2br(esc_html($c->getValue())) . "<br/>" . PHP_EOL;
                                    }
                                    else {
                                        $txt .= $txtName . ': '.esc_html($c->getValue()) . "<br/>" . PHP_EOL;
                                    }
                                }
                                if (is_a($c, \Sabre\VObject\Property\ICalendar\DateTime::class)) {
                                    /** @var \Sabre\VObject\Property\ICalendar\DateTime $c */
                                    
                                    $type = $c->name;
                                    if ($type == 'DTSTART') {
                                        $txtName = 'Start';
                                    }
                                    if ($type == 'DTEND') {
                                        $txtName = 'End';
                                    }
                                    if ($type == 'DTSTAMP') {
                                        $txtName = 'Created on';
                                    }
                                    
                                    $dt = new DateTime($c->getValue(), new DateTimeZone(date_default_timezone_get()));
                                    $html .= $txtName . ': '. $dt->format('d-m-Y H:i:s') .'<br/>'.PHP_EOL;
                                    
                                }
                                if (is_a($c, \Sabre\VObject\Component\VAlarm::class)) {
                                    /** @var \Sabre\VObject\Component\VAlarm $c */
                                }
                                
                            }
                        }
                        
                    }
                    $this->attachmentsRendered[] = $html . $txt;
                } while ($it->next());
            }
            
        }
        
        $this->setDecoratorFile( module_file('base', 'templates/decorator/blank.php') );
        
        return $this->render();
    }
    
    
    public function action_attachment() {
        /** @var \webmail\mail\render\MysqlMailRender $mail */
        $mail = $this->getMail( get_var('id') );
        
        $f = $mail->getAttachmentFile( get_var('no') );
        
        header('Content-type: ' . file_mime_type($f['filename']));
        header('Content-disposition: inline; filename="' . $f['filename'] .'"');
        print $f['content'];
    }
    
    
    
    public function action_move_mail() {
        $ms = MailSearchBase::getInstance();
        
        try {
            /** @var \webmail\search\MailSearchBase $mail */
            $mail = $ms->readById( get_var('email_id') );
            
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
            
            // close session to prevent hanging
            session_write_close();
            
            $ma = MailActionsBase::getInstance();
            if ($connector && $imapFolderId) {
                $ma->moveMail($connector, $mail, $imapFolderId);
            }
            else {
                $ma->updateFolder($mail->getId(), $newFolder);
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
        $ms = MailSearchBase::getInstance();
        
        try {
            /** @var \webmail\search\MailSearchBase $mail */
            $mail = $ms->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            // close session to prevent hanging
            session_write_close();
            
            if ( lock_webmail_mail($mail, 60 ) == false ) {
                throw new LockException( 'Unable to get lock for mail' );
            }
            
            
            
            /** @var \webmail\mail\MailProperties $mailProperties */
            $mailProperties = $mail->getProperties();
            
            $mailProperties->setAction( get_var('action') );
            $mailProperties->save();
            
            $ma = MailActionsBase::getInstance();
            $ma->updateAction($mail->getId(), get_var('action'));
            
            
            // not necessary.. exit script = release lock. Improves readability though
            release_webmail_mail($mail);
            
            
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
        $ms = MailSearchBase::getInstance();
        
        try {
            $mail = $ms->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            // close session to prevent hanging
            session_write_close();
            
            $ma = MailActionsBase::getInstance();
            $r = $ma->markAsSpam( $mail );
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
        $ms = MailSearchBase::getInstance();
        
        try {
            $mail = $ms->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            
            $ma = MailActionsBase::getInstance();
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
        $ms = MailSearchBase::getInstance();
        
        try {
            $mail = $ms->readById( get_var('email_id') );
            
            if (!$mail) {
                throw new ObjectNotFoundException('Mail not found');
            }
            
            $ma = MailActionsBase::getInstance();
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
        $ms = MailSearchBase::getInstance();
        
        /** @var \webmail\mail\render\MailRenderBase $mail */
        $mail = $ms->readById( get_var('email_id') );
        
        if (!$mail) {
            throw new ObjectNotFoundException('Mail not found');
        }
        
        
        /** @var EmailService $emailService */
        $emailService = object_container_get(EmailService::class);
        $m = $emailService->readEmailByRefMailId( $mail->getId(), ['draft_only' => true] );
        if (count($m) > 0) {
            report_user_message( t('Existing draft loaded') );
            redirect('/?m=webmail&c=view&id='.$m[0]->getEmailId());
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
        $form->getWidget('ref_mail_id')->setValue($mail->getId());
        
        
        $email = $emailService->saveEmail($form);
        
        report_user_message(t('E-mail created'));
        redirect('/?m=webmail&c=view&id='.$email->getEmailId());
    }
    
    public function action_forward() {
        $ms = MailSearchBase::getInstance();
        
        /** @var \webmail\mail\render\MailRenderBase $mail */
        $mail = $ms->readById( get_var('email_id') );
        
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
        $form->getWidget('ref_mail_id')->setValue($mail->getId());
        

        /** @var EmailService $emailService */
        $emailService = object_container_get(EmailService::class);
        $email = $emailService->saveEmail($form, $attachments);
        
        report_user_message(t('E-mail created'));
        redirect('/?m=webmail&c=view&id='.$email->getEmailId());
    }
    
}

