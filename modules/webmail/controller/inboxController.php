<?php


use base\service\MetaService;
use core\controller\BaseController;
use webmail\service\EmailService;
use webmail\mail\MailSearch;

class inboxController extends BaseController {
    
    
    public function action_index() {
        
        $user = $this->ctx->getUser();
        
        $metaService = $this->oc->get(MetaService::class);
        
        $this->state = @unserialize( $metaService->getMetaValue('user', $user->getUserId(), 'webmail-state') );
        
        $this->render();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $mailSearch = $this->oc->get(MailSearch::class);
        
        $arr = array();
        try {
//         $_REQUEST['orderby'] = 'email_id desc';
            $r = $mailSearch->search($pageNo*$limit, $limit, $_REQUEST);
            
            $arr['listResponse'] = $r;
        } catch (\Exception $ex) {
            $arr['error'] = true;
            $arr['message'] = $ex->getMessage();
        }
        
        
        $this->json($arr);
    }
    
    
    public function action_view() {
        $f = get_data_file(get_var('id'));
        if ($f == false) {
            die('Mail niet gevonden');
        }
        
        // parse mail
        $p = new \PhpMimeMailParser\Parser();
        $p->setPath($f);
        
        $html = $p->getMessageBody('html');
        if ($html) {
            $this->html = generate_safe_html($html);
        } else {
            $this->text = $p->getMessageBody('text');
        }
        
        $this->dateReceived = '';
        if ($date = $p->getHeader('date')) {
            $dt = new \DateTime(null, new \DateTimeZone('Europe/Amsterdam'));
            $dt->setTimestamp(strtotime($p->getHeader('date')));
            $this->dateReceived = $dt->format('d-m-Y H:i:s');
        }
        
        $this->from    = $p->getAddresses('from');
        $this->to      = $p->getAddresses('to');
        $this->cc      = $p->getAddresses('cc');
        $this->bcc     = $p->getAddresses('bcc');
        $this->subject = $p->getHeader('subject');
        
        $this->attachments = $p->getAttachments();
        $this->id = get_var('id');
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    public function action_attachment() {
        $f = get_data_file(get_var('id'));
        if ($f == false) {
            die('Mail niet gevonden');
        }
        
        // parse mail
        $p = new \PhpMimeMailParser\Parser();
        $p->setPath($f);
        
        $no = (int)get_var('no');
        $this->attachments = $p->getAttachments();
        
        if ($no < 0 || $no >= count($this->attachments)) {
            die('Attachment not found');
        }
        
        header('Content-type: ' . $this->attachments[$no]->getContentType());
        header('Content-disposition: inline; filename="'.$this->attachments[$no]->getFilename().'"');
        print $this->attachments[$no]->getContent();
    }
    
    
    
    
}

