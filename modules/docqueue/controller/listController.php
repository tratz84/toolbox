<?php



use core\controller\BaseController;
use docqueue\form\DocumentUploadForm;
use docqueue\service\DocqueueService;
use docqueue\model\Document;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use core\Context;

class listController extends BaseController {
    
    
    
    public function action_index() {
        
        return $this->render();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $docqueueService = $this->oc->get(DocqueueService::class);
        
        $r = $docqueueService->searchDocument($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    
    
    public function action_upload() {
        $this->form = new DocumentUploadForm();
        
        $docqueueService = $this->oc->get(DocqueueService::class);
        
        if (get_var('id')) {
            $document = $docqueueService->readDocument( get_var('id') );
        } else {
            $document = new Document();
        }
        
        $this->form->bind( $document );
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                $docqueueService->saveDocument( $this->form );
                
                redirect('/?m=docqueue&c=list');
            }
        }

        if ($document->getFilename()) {
            $this->document_id = $document->getDocumentId();
            $this->file_extension = file_extension($document->getFilename());
        } else {
            $this->file_extension = false;
        }
        
        return $this->render();
    }
    
    
    public function action_download() {
        
        $id = get_var('id');
        if (!$id) {
            throw new InvalidStateException('id not set');
        }
        
        $docqueueService = $this->oc->get(DocqueueService::class);
        $document = $docqueueService->readDocument( get_var('id') );
            
        if (!$document) {
            throw new ObjectNotFoundException('Document not found');
        }
        
        $ctx = Context::getInstance();
        $p = $ctx->getDataDir() . '/' . $document->getFilename();
        
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
        header('Content-Disposition: inline; filename="'.$document->getBasenameFile().'"');
        
        readfile($p);
    }
    
    
    
}
