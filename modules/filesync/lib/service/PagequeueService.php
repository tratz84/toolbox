<?php


namespace filesync\service;

use core\Context;
use core\exception\FileException;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use filesync\form\PagequeueUploadForm;
use filesync\model\Pagequeue;
use filesync\model\PagequeueDAO;


class PagequeueService extends ServiceBase {
    
    
    public function readPagequeue($pagequeueId) {
        $pDao = new PagequeueDAO();
        
        $p = $pDao->read( $pagequeueId );
        
        return $p;
    }
    
    
    
    public function savePage(PagequeueUploadForm $form) {
        $ctx = Context::getInstance();
        
        $id = $form->getWidgetValue('pagequeue_id');
        if ($id) {
            $pag = $this->readPagequeue($id);
        } else {
            $pag = new Pagequeue();
        }
        
        $form->fill($pag, array('pagequeue_id', 'name', 'description', 'crop_x1', 'crop_y1', 'crop_x2', 'crop_y2', 'degrees_rotated', 'page_orientation'));
        
        $pag->setUserId( ctx()->getUser()->getUserId() );
        $pag->save();
        
        if (isset($_FILES['file']) && $_FILES['file']['error'] != UPLOAD_ERR_NO_FILE) {
            $path = $pag->generatePath( $_FILES['file']['name'] );
            
            $fullpath = $ctx->getDataDir() . '/' . $path;
            
            if (copy($_FILES['file']['tmp_name'], $fullpath) == false) {
                throw new FileException('Error saving file');
            }
            
            // jpeg? => check exifdata
            if (in_array(file_extension($path), array('jpg', 'jpeg'))) {
                if ($fh = fopen($fullpath, 'r')) {
                    $exif_data = exif_read_data($fh);
                    fclose($fh);
                    
                    // Orientation set? => auto-rotate
                    if (isset($exif_data['Orientation'])) {
                        if ($exif_data['Orientation'] == 1) {
                            // default
                        }
                        else if ($exif_data['Orientation'] == 3) {
                            $pag->setDegreesRotated( 180 );
                        }
                        else if ($exif_data['Orientation'] == 6) {
                            $pag->setDegreesRotated( 90 );
                        }
                        else if ($exif_data['Orientation'] == 8) {
                            $pag->setDegreesRotated( 270 );
                        }
                    }
                }
            }
            
            
            $pag->setFilename( $path );
            $pag->save();
        }
        
        return $pag;
    }
    
    public function deletePagequeue($pagequeueId) {
        $p = $this->readPagequeue($pagequeueId);
        
        if (!$p) {
            throw new \core\exception\ObjectNotFoundException('Page not found');
        }
        
        if ($p->getFilename()) {
            $path = Context::getInstance()->getDataDir() . '/' . $p->getFilename();
            if ($path && file_exists($path)) {
                unlink($path);
            }
        }
        
        return $p->delete();
    }
    
    
    public function searchPage($start, $limit, $opts = array()) {
        $pDao = new PagequeueDAO();
        
        $cursor = $pDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('pagequeue_id', 'name', 'description', 'filename', 'basename_file', 'crop_x1', 'crop_y1', 'crop_x2', 'crop_y2', 'degrees_rotated', 'edited', 'created'));
            
        return $r;
    }
    
    
    
    
    
}
