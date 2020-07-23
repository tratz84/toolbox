<?php


namespace core\pdf;

use setasign\Fpdi\Fpdi;
use core\exception\InvalidStateException;


class MergePdf {
    
    protected $fpdi;
    protected $files = array();
    
    public function __construct() {
        
    }
    
    public function getFpdi() { return $this->fpdi; }
    public function setFpdi($p) { $this->fpdi = $p; }
    
    
    public function addFile( $f ) {
        $this->files[] = $f;
    }
    public function addFiles( $files ) {
        foreach($files as $f) {
            $this->addFile( $f );
        }
    }
    
    public function merge() {
        $this->fpdi = new Fpdi();
        
        // loop through files
        for($fc=0; $fc < count($this->files); $fc++) {
            $file = $this->files[$fc];
            
            $pageCount = $this->fpdi->setSourceFile( $file );
            
            // import pages
            for($pc=1; $pc <= $pageCount; $pc++) {
                $pageId = $this->fpdi->importPage( $pc );
                
                $pageSize = $this->fpdi->getImportedPageSize( $pageId );
                
                $this->fpdi->addPage( $pageSize['orientation'], $pageSize );
                $this->fpdi->useImportedPage( $pageId );
            }
        }
    }
    
    public function Output($dest='', $name='', $isUTF8=false) {
        if ($this->fpdi == null) {
            throw new InvalidStateException('MergePdf::merge() not called');
        }
        
        return $this->fpdi->Output( $dest, $name, $isUTF8 );
    }
    
}

