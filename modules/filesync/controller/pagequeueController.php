<?php



use core\Context;
use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use filesync\form\PagequeueEditForm;
use filesync\form\PagequeueUploadForm;
use filesync\model\Pagequeue;
use filesync\service\PagequeueService;
use core\pdf\BasePdf;
use core\exception\FileException;
use filesync\service\StoreService;

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
        $ctx = Context::getInstance();
        $this->form = new PagequeueUploadForm();
        
        $pagequeueService = $this->oc->get(PagequeueService::class);
        
        if (get_var('id')) {
            $pagequeue = $pagequeueService->readPagequeue( get_var('id') );
        } else {
            $pagequeue = new Pagequeue();
            $defaultRotation = $ctx->getSetting('filesync__pagequeue_default_rotation');
            $pagequeue->setDegreesRotated($defaultRotation);
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
    
    
    public function action_edit() {
        $pagequeueService = $this->oc->get(PagequeueService::class);
        
        $pagequeue = $pagequeueService->readPagequeue( get_var('id') );
        
        $this->form = new PagequeueEditForm();
        $this->form->bind( $pagequeue );
        
        $this->pagequeue_id = $pagequeue->getPagequeueId();
        $this->file_extension = file_extension($pagequeue->getFilename());
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    public function action_edit_save() {
        $pagequeueService = $this->oc->get(PagequeueService::class);
        
        $pagequeue = $pagequeueService->readPagequeue( get_var('pagequeue_id') );
        
        if (!$pagequeue) {
            return $this->json(array(
                'error' => true,
                'message' => 'Page not found'
            ));
        }
        
        $pagequeue->setName($_REQUEST['name']);
        $pagequeue->setDescription($_REQUEST['description']);
        
        $pagequeue->setCropX1( $_REQUEST['crop_x1'] );
        $pagequeue->setCropY1( $_REQUEST['crop_y1'] );
        $pagequeue->setCropX2( $_REQUEST['crop_x2'] );
        $pagequeue->setCropY2( $_REQUEST['crop_y2'] );
        $pagequeue->setDegreesRotated( $_REQUEST['degrees_rotated'] );
        
        if ($pagequeue->save() == false) {
            return $this->json(array(
                'error' => true,
                'message' => 'Error saving page'
            ));
        }
        
        $this->json(array(
            'success' => true,
            'id' => $pagequeue->getPagequeueId(),
            'name' => $pagequeue->getName(),
            'filename' => $pagequeue->getFilename()
        ));
    }
    
    
    
    public function action_pdf() {
        
        return $this->render();
    }
    
    public function action_pdf_generate() {
        $pagequeueService = $this->oc->get(PagequeueService::class);
        
        $pqIds = explode(',', $_REQUEST['ids']);
        
        $pqs = array();
        foreach($pqIds as $pqId) {
            $pagequeue = $pagequeueService->readPagequeue( $pqId );
            if ($pagequeue) {
                $pqs[] = $pagequeue;
            } else {
                throw new ObjectNotFoundException('Page not found ('.$pqId.')');
            }
        }
        
        if (count($pqs) == 0) {
            throw new InvalidStateException('No page selected');
        }
        
//         var_export($pqs);exit;
        
        $p = new BasePdf();
        foreach($pqs as $pq) {
            $p->AddPage();
            
            $path = Context::getInstance()->getDataDir() . '/' . $pq->getFilename();
            
            $imgsrc = new Imagick();
            $imgsrc->readImage( $path );
            $imgsrc->setimageorientation(imagick::ORIENTATION_UNDEFINED);
            
            
            if ($imgsrc->getimagewidth() > 1500) {
                $h = $imgsrc->getimageheight() / $imgsrc->getimagewidth() * 1500;
                
                $imgsrc->resizeimage(1500, $h, Imagick::FILTER_LANCZOS, 1);
            }
            
            
            $imgsrc_w = $imgsrc->getimagewidth();
            $imgsrc_h = $imgsrc->getimageheight();
            $canvasWidth = $imgsrc->getimagewidth() > $imgsrc->getimageheight() ? $imgsrc->getimagewidth() : $imgsrc->getimageheight();
            
            $img = new Imagick();
            $img->newImage($canvasWidth, $canvasWidth, 'white', 'jpg');
//             $img->setcompressionquality(10);     // doesn't do anything?
            $img->compositeimage($imgsrc, Imagick::COMPOSITE_OVER, $canvasWidth/2 - $imgsrc_w/2, $canvasWidth/2 - $imgsrc_h/2);
            
            $imgsrc->destroy();
            
            $img->rotateimage('#ffffff', $pq->getDegreesRotated());
            
            if ($pq->getCropX1() != 0 || $pq->getCropY1() != 0 || $pq->getCropX2() != 100 || $pq->getCropY2() != 100) {
                $cw = ($pq->getCropX2() - $pq->getCropX1()) * $canvasWidth / 100;
                $ch = ($pq->getCropY2() - $pq->getCropY1()) * $canvasWidth / 100;
                
                $cx = $pq->getCropX1() / 100 * $canvasWidth;
                $cy = $pq->getCropY1() / 100 * $canvasWidth;
                
                $img->cropimage($cw, $ch, $cx, $cy);
            }
            
            
            // calculate size in pdf
            $pw = $p->GetPageWidth();
            $ph = $p->GetPageHeight();
            
            
            $margin = 0.1;
            $piw = $pw - ($pw * $margin);
            $pih = $ph - ($ph * $margin);
            
            
            $w = $piw;
            $h = $pih / $w * $piw;
            if ($h > $pih) {
                $h = $pih;
                $w = $piw / $h * $pih;
            }
            
            // add to pdf
            $p->ImagickJpeg($pq->getFilename(), $img, $pw/2-$w/2, ($ph*$margin)/4, $w, $h);
            
            // destroy image
            $img->destroy();
            
        }
        
        $p->AliasNbPages();

        
        // generate file
        $path = Context::getInstance()->getDataDir() . '/tmp';
        if (is_dir($path) == false) {
            if (mkdir($path, 0755) == false) {
                throw new FileException('Unable to create temp-folder');
            }
        }
        
        
        // fetch archivestore
        $ctx = Context::getInstance();
        $storeId = $ctx->getSetting('filesync__pagequeue_archive_store');
        $storeService = object_container_get(StoreService::class);
        $storeArchive= $storeService->readStore($storeId);
        if ($storeArchive == null || $storeArchive->getStoreType() != 'archive') {
            throw new InvalidStateException('No archive-store configured');
        }
        
        $pdffile = $path . '/pq-'.date('Ymd-His') . '.pdf';
        $p->Output('F', $pdffile);
        
        // save to store
        $storefile = $storeService->syncFile($storeArchive->getStoreId(), basename($pdffile), md5_file($pdffile), filesize($pdffile), date('Y-m-d H:i:s'), false, $pdffile);
        
        // remove tempfile
        unlink($pdffile);
        
        
        // delete files
        if (get_var('delete_files')) {
            foreach($pqs as $pq) {
                 $pagequeueService->deletePagequeue( $pq->getPagequeueId() );
            }
        }
        
        
        redirect('/?m=filesync&c=storefile&a=edit_meta&store_file_id='.$storefile->getStoreFileId());
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
