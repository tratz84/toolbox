<?php


use core\container\ArrayContainer;
use core\exception\FileException;
use core\exception\ObjectNotFoundException;
use filesync\service\StoreService;
use base\service\SettingsService;

function mapArchiveStores() {
    $storeService = object_container_get(StoreService::class);
    
    $mapStores = array();
    $archiveStores = $storeService->readArchiveStores();
    foreach($archiveStores as $as) {
        $mapStores[ $as->getStoreId() ] = $as->getStoreName();
    }
    
    return $mapStores;
}


function filesync_lookup_libreoffice() {
    $paths = array();
    if (defined('SOFFICE_BIN'))
        $paths[] = SOFFICE_BIN;
    $paths[] = "/usr/bin/soffice";
    
    foreach($paths as $p) {
        if (file_exists($p)) {
            return $p;
        }
    }
    
    return false;
}


function filesync_convert_to_pdf($filename) {
    $soffice = filesync_lookup_libreoffice();
    
    if (!$soffice) {
        return false;
    }
    
    // fetch temp-folder
    $f = get_data_file('/tmp');
    if ($f == false) {
        $f = ctx()->getDataDir();
        
        if (mkdir($f . '/tmp', 0755) == false) {
            throw new FileException('Unable to create temp-folder');
        }
        
        $f = get_data_file('/tmp');
    }
    if (!$f) {
        throw new FileException('Temp-folder not found');
    }
    
    $cmd = '"'.$soffice . '"' . ' --headless --convert-to pdf --outdir '.escapeshellcmd($f).' '.escapeshellarg($filename);
    `$cmd`;
    
    $pdffile = basename($filename);
    if (strpos($pdffile, '.') !== false) {
        $pdffile = substr($pdffile, 0, strrpos($pdffile, '.')) . '.pdf';
    } else {
        $pdffile = $pdffile . '.pdf';
    }
    
    $p = $f . '/' . $pdffile;
    if (file_exists($p)) {
        return $p;
    } else {
        return false;
    }
}

function filesync_storefile2pdf( $storeFileId ) {
    $storeService = object_container_get( StoreService::class );
    
    $sf = $storeService->readStoreFile( $storeFileId );
    
    if (!$sf) {
        throw new ObjectNotFoundException('StoreFile not found');
    }
    
    $rev = $sf->getLastRevision();
    
    $file = get_data_file('/filesync/'.$sf->getStoreId() . '/' . $sf->getStoreFileId() . '-' . $rev->getStoreFileRevId());
    if (!$file) {
        return false;
    }
    
    if (file_exists($file . '-preview.pdf')) {
        return $file . '-preview.pdf';
    }
    
    $pdffile = filesync_convert_to_pdf( $file );
    if (!$pdffile) {
        return false;
    }
    
    if (!copy($pdffile, $file.'-preview.pdf')) {
        throw new FileException('Unable to copy preview-file');
    }
    
    unlink( $pdffile );
    
    return $file.'-preview.pdf';
    
}



function filesync_filetemplates() {
    $ac = new ArrayContainer();
    hook_eventbus_publish($ac, 'filesync', 'filetemplates');
    
    $storeService = object_container_get( StoreService::class );
    
    for($x=0; $x < $ac->count(); $x++) {
        $ft = $ac->get($x);
        
        $storeFileId = ctx()->getSetting('filetemplate__'.$ft->getId());
        
//         var_export($storeFileId);exit;
        if ($storeFileId) {
            $storeFile = $storeService->readStoreFile( $storeFileId );
            
            $ft->setStoreFileId( $storeFile->getStoreFileId() );
            $ft->setFile( $storeFile->getPath() );
        }
        
        $ac->set($x, $ft);
    }
    
    
    return $ac;
}



