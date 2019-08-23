<?php


namespace docqueue\service;

use core\service\ServiceBase;
use docqueue\model\DocumentDAO;
use docqueue\model\Document;
use docqueue\form\DocumentUploadForm;
use core\Context;
use core\exception\FileException;
use core\forms\lists\ListResponse;


class DocqueueService extends ServiceBase {
    
    
    public function readDocument($documentId) {
        $dDao = new DocumentDAO();
        
        $d = $dDao->read( $documentId );
        
        return $d;
    }
    
    
    
    public function saveDocument(DocumentUploadForm $form) {
        $ctx = Context::getInstance();
        
        $doc = new Document();
        $form->fill($doc, array('document_id', 'name', 'description'));
        $doc->save();
        
        if (isset($_FILES['file'])) {
            $path = $doc->generatePath( $_FILES['file']['name'] );
            
            $fullpath = $ctx->getDataDir() . '/' . $path;
            
            if (copy($_FILES['file']['tmp_name'], $fullpath) == false) {
                throw new FileException('Error saving file');
            }
            
            $doc->setFilename( $path );
            $doc->save();
        }
        
        return $doc;
    }
    
    
    
    public function searchDocument($start, $limit, $opts = array()) {
        $dDao = new DocumentDAO();
        
        $cursor = $dDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('document_id', 'name', 'description', 'filename', 'basename_file', 'edited', 'created'));
        
        return $r;
    }
    
    
    
    
    
}
