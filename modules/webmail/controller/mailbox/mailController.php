<?php



use core\controller\BaseController;
use webmail\solr\SolrMailQuery;
use webmail\solr\SolrMailQueryResponse;
use webmail\solr\SolrMail;

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
    
    
    
}

