<?php


namespace filesync\model;

use core\Context;
use core\exception\FileException;
use core\exception\InvalidStateException;


class Pagequeue extends base\PagequeueBase {

    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setCropX1(0);
        $this->setCropY1(0);
        $this->setCropX2(100);
        $this->setCropY2(100);
        $this->setDegreesRotated(0);
        $this->setPageOrientation('P');
    }
    
    
    
    public function getBasenameFile() {
        return basename($this->getFilename());
    }
    
    
    
    public function generatePath($filename) {
        if (!$this->getCreated()) {
            throw new InvalidStateException('Page not saved');
        }
        
        // build path folder
        $t = date2unix( $this->getCreated() );
        $dir = 'pagequeue/' . date('Y', $t) . '/' . date('n', $t) . '/';
        
        // create folder
        $fulldir = Context::getInstance()->getDataDir() . '/' . $dir;
        if (is_dir($fulldir) == false) {
            if (mkdir($fulldir, 0755, true) == false) {
                throw new FileException('Unable to create folder');
            }
        }
        
        // create unique filename
        $file = $this->getPagequeueId() . '-' . basename($filename);
        
        // hoppa
        return $dir . $file;
    }
    
}

