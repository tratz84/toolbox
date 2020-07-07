<?php


namespace webmail\service;


use base\util\ActivityUtil;
use core\Context;
use core\exception\FileException;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use webmail\form\IdentityForm;
use webmail\model\Email;
use webmail\model\EmailDAO;
use webmail\model\EmailFile;
use webmail\model\EmailFileDAO;
use webmail\model\EmailToDAO;
use webmail\model\Identity;
use webmail\model\IdentityDAO;
use webmail\form\EmailForm;
use customer\model\CompanyEmailDAO;
use webmail\form\MailSettingsOutForm;
use base\service\SettingsService;
use webmail\mail\SendMail;
use core\parser\HtmlParser;

class EmailService extends ServiceBase {
    
    
    
    public function readAllIdentities() {
        $iDao = new IdentityDAO();
        
        return $iDao->readAll();
    }
    
    public function readActiveIdentities() {
        $iDao = new IdentityDAO();
        
        return $iDao->readActive();
    }
    
    public function readFirstIdentity() {
        $ids = $this->readActiveIdentities();
        
        if (count($ids)) {
            return $ids[0];
        } else {
            return null;
        }
    }
    
    public function readIdentity($id) {
        $iDao = new IdentityDAO();
        
        return $iDao->read($id);
    }
    
    public function readSystemMessagesIdentity() {
        $iDao = new IdentityDAO();
        
        $i = $iDao->readSystemMessages();
        
        if ($i === null) {
            $i = new Identity();
            $i->setFromName('Toolbox - Admin');
            $i->setFromEmail('no-reply@localhost');
        }
        
        return $i;
    }
    
    public function saveIdentity(IdentityForm $form) {
        $id = $form->getWidgetValue('identity_id');
        if ($id) {
            $identity = $this->readIdentity($id);
        } else {
            $identity = new Identity();
        }
        
        $form->fill($identity, array('identity_id', 'connector_id', 'from_name', 'from_email', 'active', 'system_messages'));
        
        // set to null if not set
        if (!$identity->getConnectorId()) {
            $identity->setConnectorId(null);
        }
        
        
        if ($identity->getSystemMessages()) {
            $iDao = object_container_create(IdentityDAO::class);
            $iDao->unsetSystemMessageFlag();
        }
        
        $identity->save();
        
        return $identity;
    }
    
    public function deleteIdentity($id) {
        $iDao = new IdentityDAO();
        $iDao->delete($id);
    }
    
    public function updateIdentitySort($identityIds) {
        if (is_string($identityIds)) {
            $identityIds = explode(',', $identityIds);
        }
        
        $iDao = new IdentityDAO();
        $iDao->updateSort($identityIds);
        
    }
    
    public function readEmail($emailId) {
        $eDao = new EmailDAO();
        
        $email = $eDao->read($emailId);
        
        if (!$email)
            return null;
        
        $etDao = new EmailToDAO();
        $recipients = $etDao->readByEmail($email->getEmailId());
        $email->setRecipients( $recipients );
        
        
        $efDao = new EmailFileDAO();
        $emailFiles = $efDao->readByEmail($email->getEmailId());
        $email->setFiles( $emailFiles );
        
        
        return $email;
    }

    public function saveEmailObject( $email, $attachments=array() ) {
        
        if (!$email->save()) {
            return false;
        }
        
        
        $etDao = new EmailToDAO();
        $arrRecipients = $email->getRecipients();
        $etDao->mergeFormListMTO1('email_id', $email->getEmailId(), $arrRecipients);
        
        // add attachments
        foreach($attachments as $att) {
            $this->addFile($email->getEmailId(), $att['filename'], $att['content']);
        }
    }
    
    /**
     * 
     * @param EmailForm $form
     * @param array $attachments, example:
     *                  array(
     *                      array('filename' => 'file.pdf', 'content' => '....'),
     *                  )
     *                  
     * @return \webmail\model\Email|false
     */
    public function saveEmail(EmailForm $form, $attachments=array()) {
        $id = $form->getWidgetValue('email_id');
        if ($id) {
            $email = $this->readEmail($id);
        } else {
            $email = new Email();
            
            $email->setStatus( $form->getWidgetValue('status') );
            $email->setIncoming( $form->getWidgetValue('incoming') ? true : false );
            $email->setSolrMailId( $form->getWidgetValue('solr_mail_id') );
        }
        
        $form->fill($email, array('email_id', 'identity_id', 'subject', 'text_content', 'recipients', 'company_id', 'person_id'));
        
        if ($email->getIdentityId()) {
            $identity = $this->readIdentity($email->getIdentityId());
            
            if ($identity) {
                $email->setFromName($identity->getFromName());
                $email->setFromEmail($identity->getFromEmail());
            }
        }
        
        
        // TODO: bind recipients + attachments to e-mail object
        // TODO: return $this->saveEmailObject( $email ); ....
        
        if (!$email->save()) {
            return false;
        }
        
        
        $etDao = new EmailToDAO();
        $arrRecipients = $form->getWidget('recipients')->getObjects();
        $etDao->mergeFormListMTO1('email_id', $email->getEmailId(), $arrRecipients);
        
        // add attachments
        foreach($attachments as $att) {
            $this->addFile($email->getEmailId(), $att['filename'], $att['content']);
        }
        
        return $email;
    }
    
    
    public function markMailAsSent($emailId) {
        $email = $this->readEmail($emailId);
        
        $eDao = new EmailDAO();
        $eDao->markAsSent($emailId);
        
        ActivityUtil::logActivity($email->getCompanyId(), $email->getPersonId(), 'webmail__email', $email->getEmailId(), 'email-sent', 'E-mail verstuurd: '.$email->getSubject(), null, $email->getFields());
    }
    
    
    public function searchEmail($start, $limit, $opts=array()) {
        $eDao = new EmailDAO();
        
        $cursor = $eDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('email_id', 'user_id', 'incoming', 'from_name', 'from_email', 'subject', 'deleted', 'created', 'status', 'company_name', 'firstname', 'insert_lastname', 'lastname'));
        
        return $r;
    }
    
    
    public function deleteEmail($emailId) {
        $email = $this->readEmail($emailId);
        
        if (!$email)
            return false;
        
        $files = $email->getFiles();
        foreach($files as $f) {
            $this->deleteFile($email->getEmailId(), $f->getEmailFileId());
        }
        
        $r = $email->delete();
        
        // text_content might be too long for log
        try {
            $hp = new HtmlParser();
            $hp->loadString( $email->getTextContent() );
            $hp->parse();
            $txt = trim( $hp->getBodyText() );
            if (strlen($txt) > 65000) {
                $txt = substr($txt, 0, 65000);
            }
            $email->setTextContent( $txt );
        } catch (\Exception $ex) {
            $email->setTextContent('');
        } catch (\Error $err) {
            $email->setTextContent('');
        }
        
        ActivityUtil::logActivity($email->getCompanyId(), $email->getPersonId(), 'webmail__email', $email->getEmailId(), 'email-deleted', 'E-mail verwijderd: '.$email->getSubject(), null, $email->getFields());
        
        return $r;
    }
    
    
    
    public function createDraft(Email $email, $files=array()) {
        $ctx = Context::getInstance();
        $datadir = $ctx->getDataDir();
        
        $emaildir = $datadir . '/emailfiles';
        
        if (file_exists($emaildir) == false) {
            if (mkdir($emaildir, 0755) == false)
                throw new FileException('Unable to create e-mail file folder');
        }
        
        $email->save();
        
        foreach($email->getRecipients() as $r) {
            $r->setEmailId($email->getEmailId());
            $r->save();
        }
        
        foreach($files as $file) {
            $this->addFile($email->getEmailId(), $file['filename'], $file['data']);
        }
        
        ActivityUtil::logActivity($email->getCompanyId(), $email->getPersonId(), 'webmail__email', $email->getEmailId(), 'email-created', 'E-mail aangemaakt: '.$email->getSubject(), null, $email->getFields());
    }
    
    public function addFileByPath($emailId, $filename, $tmpFilePath) {
        $data = file_get_contents($tmpFilePath);
        
        return $this->addFile($emailId, $filename, $data);
    }
    
    public function addFile($emailId, $filename, $data) {
        $ctx = Context::getInstance();
        $datadir = $ctx->getDataDir();
        
        $el = new EmailFile();
        $el->setEmailId($emailId);
        $el->setFilename($filename);
        $el->save();
        
        $path = 'emailfiles/' . $el->getEmailFileId() . '-' . $filename;
        if (file_put_contents($datadir.'/'.$path, $data) === false) {
            throw new FileException('Unable to write file');
        }
        
        $el->setPath($path);
        return $el->save();
    }
    
    public function readFile($emailFileId) {
        $elDao = new EmailFileDAO();
        return $elDao->read($emailFileId);
    }
    
    
    public function deleteFile($emailId, $emailFileId) {
        $f = $this->readFile($emailFileId);
        
        if (!$f)
            return null;
        
        if ($f->getEmailId() != $emailId)
            return null;

        $ctx = Context::getInstance();
        $datadir = $ctx->getDataDir();
        
        $f->delete();
        
        if (file_exists($datadir.'/'.$f->getPath()))
            unlink($datadir.'/'.$f->getPath());
    }
    
    
    public function saveEmailFile($connectorId, $folderName, $file) {
        $p = new \PhpMimeMailParser\Parser();
        $p->setPath($file);
        
        $o = new Email();
        $arrFrom = $p->getAddresses('from');
        if (count($arrFrom)) {
            if (isset($arrFrom[0]['display'])) {
                $o->setFromName($arrFrom[0]['display']);
            }
            if (isset($arrFrom[0]['address'])) {
                $o->setFromEmail($arrFrom[0]['address']);
            }
        }

        $dt = new \DateTime(null, new \DateTimeZone('+0000'));
        $dt->setTimestamp(strtotime($p->getHeader('date')));
        
        $o->setSubject($p->getHeader('subject'));
        $o->setMessageId(basename($file));
        $o->setSearchId( crc32($o->getMessageId()) );
        $o->setCreated(date('Y-m-d H:i:s'));
        $o->setIncoming(true);
        $o->setReceived($dt->format('Y-m-d H:i:s'));    
        $o->setStatus('open');
        
        if (in_array(strtolower($folderName), ['spam', 'junk'])) {
            $o->setSpam(true);
        } else {
            $o->setSpam(false);
        }
        
        // check if mail is already imported
        $eDao = new EmailDAO();
        $check = $eDao->readReceived( $o->getSearchId(), $o->getMessageId(), $o->getReceived() );
        if ($check) {
            return $check;
        }
        
        // new? => save
        $o->save();
        
        return $o;
    }
    
    
    public function saveMailServerSettings(MailSettingsOutForm $form) {
        $server_type   = trim( $form->getWidgetValue('server_type') );
        $mail_hostname = trim( $form->getWidgetValue('mail_hostname') );
        $mail_port     = trim( $form->getWidgetValue('mail_port') );
        $mail_username = trim( $form->getWidgetValue('mail_username') );
        $mail_password = trim( $form->getWidgetValue('mail_password') );
        
        $settingsService = object_container_get( SettingsService::class );
        $settingsService->updateValue('webmail_server_type',   $server_type);
        $settingsService->updateValue('webmail_mail_hostname', $mail_hostname);
        $settingsService->updateValue('webmail_mail_port',     $mail_port);
        $settingsService->updateValue('webmail_mail_username', $mail_username);
        if ($mail_password) {
            $settingsService->updateValue('webmail_mail_password', $mail_password);
        }
        
        $ctx = object_container_get(Context::class);
        $ctx->flushSettingCache();
    }
    
    public function getMailServerSettings() {
        /** @var Context */
        $ctx = object_container_get(Context::class);
        
        $s = array();
        $s['server_type']   = $ctx->getSetting('webmail_server_type');
        $s['mail_hostname'] = $ctx->getSetting('webmail_mail_hostname');
        $s['mail_port']     = $ctx->getSetting('webmail_mail_port');
        $s['mail_username'] = $ctx->getSetting('webmail_mail_username');
        $s['mail_password'] = $ctx->getSetting('webmail_mail_password');
        
        // default to local
        if ($s['server_type'] != 'local' && $s['server_type'] != 'smtp')
            $s['server_type'] = 'local';
        
        return $s;
    }
    
    public function sendMailTest($emailAddress) {
        $sm = new SendMail();
        
        $sm->setFromName('Toolbox - Test');
        if (validate_email($sm->getFromEmail()) == false)
            $sm->setFromEmail('toolboxtest@itxplain.nl');
        $sm->setSubject('Toolbox test mail');
        $sm->addTo($emailAddress);
        
        $tpl = module_file('webmail', 'templates/settingsMailOut/_testmail.php');
        $content = get_template($tpl, array());
        $sm->setContent( $content );
        
        return $sm->send();
    }
    
    
}

