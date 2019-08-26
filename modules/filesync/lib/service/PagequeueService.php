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
        
        $pag = new Pagequeue();
        $form->fill($pag, array('pagequeue_id', 'name', 'description'));
        $pag->save();
        
        if (isset($_FILES['file'])) {
            $path = $pag->generatePath( $_FILES['file']['name'] );
            
            $fullpath = $ctx->getDataDir() . '/' . $path;
            
            if (copy($_FILES['file']['tmp_name'], $fullpath) == false) {
                throw new FileException('Error saving file');
            }
            
            $pag->setFilename( $path );
            $pag->save();
        }
        
        return $pag;
    }
    
    
    
    public function searchPage($start, $limit, $opts = array()) {
        $pDao = new PagequeueDAO();
        
        $cursor = $pDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('pagequeue_id', 'name', 'description', 'filename', 'basename_file', 'edited', 'created'));
            
        return $r;
    }
    
    
    
    
    
}
