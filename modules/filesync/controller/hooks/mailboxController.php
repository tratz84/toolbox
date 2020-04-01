<?php


use core\controller\BaseController;
use webmail\solr\SolrMailQuery;
use core\exception\ObjectNotFoundException;
use core\exception\InvalidStateException;
use filesync\service\StoreService;
use core\forms\SelectField;
use core\exception\FileException;

class mailboxController extends BaseController {
    
    
    
    public function action_index() {
        $smq = new SolrMailQuery();
        /** @var \webmail\solr\SolrMail $mail */
        $this->mail = $smq->readById(get_var('email_id') );
        
        if ($this->mail == null) {
            throw new ObjectNotFoundException('Mail not found');
        }
        
        $this->attachments = $this->mail->getAttachments();
        
        $this->htmlToPdfAvailable = toolbox_html2pdf_available();
        
        
        $mapArchiveStores = array();
        $storeService = object_container_get(StoreService::class);
        $ass = $storeService->readArchiveStores();
        foreach($ass as $as) {
            $mapArchiveStores[$as->getStoreId()] = $as->getStoreName();
        }
        $this->selectArchiveStore = new SelectField('store_id', '', $mapArchiveStores, 'Archive store');

        
        if (count($mapArchiveStores) == 0) {
            $this->importAvailable = false;
        }
        else if (count($this->attachments) > 0 || $this->htmlToPdfAvailable == true) {
            $this->importAvailable = true;
        } else {
            $this->importAvailable = false;
        }
        
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    
    
    public function action_import() {
        $smq = new SolrMailQuery();
        /** @var \webmail\solr\SolrMail $mail */
        $this->mail = $smq->readById(get_var('email_id') );
        
        if ($this->mail == null) {
            throw new ObjectNotFoundException('Mail not found');
        }
        
        $attachmentNo = get_var('attachmentNo');
        $attachments = $this->mail->getAttachments();
        
        // validate attachmentNo
        if (is_numeric($attachmentNo) == false || $attachmentNo < -1 || $attachmentNo >= count($attachments)) {
            throw new InvalidStateException('Attachment not found');
        }
        
        $attachment = null;
        if ($attachmentNo == -1) {
            // html2pdf
            $pdfdata = toolbox_html2pdf( $this->mail->getContent() );
            
            $attachment = array(
                'filename' => 'mail.pdf',
                'content' => $pdfdata
            );
        }
        else {
            $attachment = $this->mail->getAttachmentFile( $attachmentNo );
        }
        
        $storeId = (int)get_var('store_id');
        $storeService = object_container_get(StoreService::class);
        
        $md5sum = md5($attachment['content']);
        $filesize = strlen($attachment['content']);
        $lastmodified = date('Y-m-d H:i:s');
        $encrypted = false;
        
        $tmpfile = 'tmp/'.date('YmdHis').'-'.$md5sum.'.'.file_extension($attachment['filename']);
        $path = save_data($tmpfile, $attachment['content']);
        
        if ($path == false) {
            throw new FileException('Error saving file');
        }
        
        $fullpath = get_data_file($path);
        
        if (!$fullpath) {
            throw new FileException('Error saving file (2)');
        }
        
        $storefile = $storeService->syncFile($storeId, $attachment['filename'], $md5sum, $filesize, $lastmodified, $encrypted, $fullpath);
        
        if (!$storefile) {
            throw new InvalidStateException('StoreFile not created (??)');
        }
        
        unlink($fullpath);
        
        
        redirect('/?m=filesync&c=storefile&a=edit_meta&store_file_id='.$storefile->getStoreFileId());
    }
    
    
    
}
