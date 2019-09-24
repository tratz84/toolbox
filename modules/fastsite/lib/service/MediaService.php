<?php


namespace fastsite\service;

use core\service\ServiceBase;
use fastsite\form\MediaFileForm;
use core\Context;
use core\exception\FileException;

class MediaService extends ServiceBase {
    
    
    
    public function saveUpload(MediaFileForm $form) {
        $ctx = Context::getInstance();
        $p = $ctx->getDataDir() . '/fastsite/fs-media';
        
        if (file_exists($p) == false) {
            if (mkdir($p, 0755, true) == false) {
                throw new FileException('No access to data directory');
            }
        }
        
        $n = $_FILES['file']['name'];
        if (copy($_FILES['file']['tmp_name'], $p . '/' . basename($n)) == false) {
            throw new FileException('Error copying file to data directory');
        }
    }
    
    
}
