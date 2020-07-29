<?php



use core\controller\BaseController;
use core\exception\ObjectNotFoundException;
use filesync\service\StoreService;
use core\exception\InvalidStateException;
use core\container\ActionContainer;

class storefileController extends BaseController {
    
    
    
    public function action_index() {
        checkCapability('filesync', 'manager');
        
        $storeService = $this->oc->get(StoreService::class);
        
        $this->store = $storeService->readStore(get_var('id'));
        
        if ($this->store == null) {
            throw new ObjectNotFoundException('Store not found');
        }
        
        if ($this->store->getStoreType() == 'archive') {
            $this->setActionTemplate( 'index_archive' );
        }
        
        $this->render();
    }
    
    
    public function action_search() {
        checkCapability('filesync', 'manager');
        
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $storeService = $this->oc->get(StoreService::class);
        
        $r = $storeService->searchFile($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_edit() {
        checkCapability('filesync', 'manager');
        
        $storeService = $this->oc->get(StoreService::class);
        
        
        $this->storeFile = $storeService->readStoreFile( get_var('store_file_id') );
        if (!$this->storeFile) {
            throw new ObjectNotFoundException('File not found');
        }
        
        $this->store = $storeService->readStore($this->storeFile->getStoreId());
        
        $this->form = $storeService->readFilemeta( get_var('store_file_id') );
        
        if ($this->form == null) {
            throw new ObjectNotFoundException('File not found');
        }
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $storeService->saveFilemeta($this->form);
                
                $sf_id = (int)$this->form->getWidgetValue('store_file_id');
                
                report_user_message(t('Changes saved'));
                redirect('/?m=filesync&c=storefile&a=edit&store_file_id=' . $sf_id);
            }
        }
        
        $this->revisions = $this->storeFile->getRevisions();
        $this->revisions = array_reverse($this->revisions);
        
        $this->render();
    }
    
    
    public function action_edit_meta() {
        checkCapability('filesync', 'manager');
        
        $storeService = $this->oc->get(StoreService::class);
        $this->form = $storeService->readFilemeta( get_var('store_file_id') );
        
        if ($this->form == null) {
            throw new ObjectNotFoundException('File not found');
        }
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $storeService->saveFilemeta($this->form);
                
                $sf_id = $this->form->getWidgetValue('store_file_id');
                
                report_user_message(t('Changes saved'));
                redirect( '/?m=filesync&c=storefile&a=edit_meta&store_file_id=' . $sf_id );
            }
        }
        
        
        $this->actionContainer = new ActionContainer('store-file-meta-edit', $this->form->getWidgetValue('store_file_id'));
        hook_eventbus_publish($this->actionContainer, 'filesync', 'store-file-meta-edit');
        
        
        $this->render();
    }
    
    
    public function action_download() {
        
        $storeService = $this->oc->get(StoreService::class);
        
        $sf = $storeService->readStoreFile( get_var('id') );
        if ($sf == null) {
            throw new ObjectNotFoundException('File not found');
        }
        
        $rev = null;
        
        if (get_var('rev')) {
            $revno = (int)get_var('rev');
        } else {
            $revno = $sf->getLastRevision()->getRev();
        }
        
        if ($revno) foreach($sf->getRevisions() as $r) {
            if ((int)$r->getRev() == $revno) {
                $rev = $r;
                break;
            }
        }
        if ($rev == null) {
            throw new ObjectNotFoundException('Revision not found');
        }
        
        if ($rev == null) {
            $rev = $sf->getLastRevision();
        }
        
        $file = get_data_file('/filesync/'.$sf->getStoreId() . '/' . $sf->getStoreFileId() . '-' . $rev->getStoreFileRevId());
        
        if (!$file) {
            throw new ObjectNotFoundException('File not found');
        }
        
        header('Content-type: ' . mime_content_type ($file));
        header('Content-Disposition: '.(get_var('inline')?'inline':'attachment').'; filename="'.$sf->getFilename().'"');
        
        
        readfile($file);
    }
    
    
    public function action_pdf_preview() {
        
        $storeService = $this->oc->get(StoreService::class);
        
        $sf = $storeService->readStoreFile( get_var('id') );
        if ($sf == null) {
            throw new ObjectNotFoundException('File not found');
        }
        
        
        $pdffile = filesync_storefile2pdf( $sf->getStoreFileId() );
        
        if (!$pdffile) {
            die('Error creating pdf-file');
        }
        
        header('Content-type: ' . mime_content_type ($pdffile));
        header('Content-Disposition: inline; filename="'.basename($pdffile).'"');
        
        
        readfile( $pdffile );
    }
    
    
    
    public function action_delete() {
        $storeService = $this->oc->get(StoreService::class);
        
        // delete by revision
        if (get_var('store_file_rev_id')) {
            $storeFile = $storeService->readStoreFileByRev(get_var('store_file_rev_id'));
            if (!$storeFile) {
                throw new ObjectNotFoundException('File not found');
            }
            
            $storeService->deleteStoreFileRev( get_var('store_file_rev_id') );
            
            if (count($storeFile->getRevisions()) == 1) {
                report_user_message(t('File deleted'));
                redirect('/?m=filesync&c=storefile&id=' . $storeFile->getStoreId());
            } else {
                report_user_message(t('Revision deleted'));
                redirect('/?m=filesync&c=storefile&a=edit&store_file_id='.$storeFile->getStoreFileId());
            }
       
        }
        else if (get_var('store_file_id')) {
            $storeFile = $storeService->readStoreFile( get_var('store_file_id') );
            if (!$storeFile) {
                throw new ObjectNotFoundException('File not found');
            }
            
            $storeService->deleteFile(get_var('store_file_id'));
            
            report_user_message(t('File deleted'));
            redirect('/?m=filesync&c=storefile&id=' . $storeFile->getStoreId());
        }
        
        throw new InvalidStateException('No id');
    }
    
    
}

