<?php




use core\controller\BaseController;
use webmail\mail\SolrMailActions;
use webmail\solr\SolrMailQuery;
use core\exception\ObjectNotFoundException;
use webmail\service\ConnectorService;

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
    
    
}

