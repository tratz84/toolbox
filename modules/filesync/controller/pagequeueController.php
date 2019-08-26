<?php



use core\Context;
use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use filesync\form\PagequeueUploadForm;
use filesync\service\PagequeueService;
use filesync\model\Pagequeue;

class pagequeueController extends BaseController {
    
    
    
    public function action_index() {
        
        return $this->render();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $pagequeueService = $this->oc->get(PagequeueService::class);
        
        $r = $pagequeueService->searchPage($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    
    
    public function action_upload() {
        $this->form = new PagequeueUploadForm();
        
        $pagequeueService = $this->oc->get(PagequeueService::class);
        
        if (get_var('id')) {
            $pagequeue = $pagequeueService->readPagequeue( get_var('id') );
        } else {
            $pagequeue = new Pagequeue();
        }
        
        $this->form->bind( $pagequeue );
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                $pagequeueService->savePage( $this->form );
                
                redirect('/?m=filesync&c=pagequeue');
            }
        }
        
        if ($pagequeue->getFilename()) {
            $this->pagequeue_id = $pagequeue->getPagequeueId();
            $this->file_extension = file_extension($pagequeue->getFilename());
        } else {
            $this->file_extension = false;
        }
        $this->isNew = $pagequeue->isNew();
        
        return $this->render();
    }
    
    
    public function action_download() {
        
        $id = get_var('id');
        if (!$id) {
            throw new InvalidStateException('id not set');
        }
        
        $pagequeueService = $this->oc->get(PagequeueService::class);
        $page = $pagequeueService->readPagequeue( get_var('id') );
            
        if (!$page) {
            throw new ObjectNotFoundException('Page not found');
        }
        
        $ctx = Context::getInstance();
        $p = $ctx->getDataDir() . '/' . $page->getFilename();
        
        // Content-type
        $mimetype = false;
        if (function_exists('mime_content_type')) {
            $mimetype = mime_content_type($p);
            if ($mimetype) {
                header('Content-Type: ' . $mimetype);
            }
        }
        if ($mimetype == false) {
            header('Content-Type: application/octet-stream');
        }
        
        // Content-Disposition
        header('Content-Disposition: inline; filename="'.$page->getBasenameFile().'"');
        
        readfile($p);
    }
    
    
    public function action_delete() {
        $pagequeueService = $this->oc->get(PagequeueService::class);
        
        $pagequeueService->deletePagequeue( get_var('id') );
        
        redirect('/?m=filesync&c=pagequeue');
    }
    
    
    
}
