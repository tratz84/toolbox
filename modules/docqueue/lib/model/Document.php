<?php


namespace docqueue\model;


use core\exception\InvalidStateException;
use core\Context;
use core\exception\FileException;

class Document extends base\DocumentBase {

    
    public function getBasenameFile() {
        return basename($this->getFilename());
    }

    
    
    public function generatePath($filename) {
        if (!$this->getCreated()) {
            throw new InvalidStateException('Document not saved');
        }
        
        // build path folder
        $t = date2unix( $this->getCreated() );
        $dir = 'docqueue/' . date('Y', $t) . '/' . date('n', $t) . '/';
        
        // create folder
        $fulldir = Context::getInstance()->getDataDir() . '/' . $dir;
        if (is_dir($fulldir) == false) {
            if (mkdir($fulldir, 0755, true) == false) {
                throw new FileException('Unable to create folder');
            }
        }
        
        // create unique filename
        $file = $this->getDocumentId() . '-' . basename($filename);
        
        // hoppa
        return $dir . $file;
    }
    
}

