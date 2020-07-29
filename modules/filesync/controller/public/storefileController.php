<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use core\exception\SecurityException;
use filesync\service\StoreService;
use filesync\model\StoreFileDownloadLog;

class storefileController extends BaseController {
    
    
    public function action_download() {
        
        $storeService = object_container_get(StoreService::class);
        
        
        if (!get_var('sfid')) {
            throw new InvalidStateException('No file specified');
        }
        
        // check if file exists
        $sf = $storeService->readStoreFile( get_var('sfid') );
        if ($sf == null) {
            throw new InvalidStateException('File not found');
        }
        
        // file marked public?
        $meta = $storeService->readFilemeta( $sf->getStoreFileId() );
        if ($meta->getWidgetValue('public') == false) {
            throw new InvalidStateException('File not public');
        }
        
        
        // check public_secret
        $ps = $meta->getWidgetValue('public_secret');
        if (!$meta || strlen(trim($ps)) < 16 || $ps != get_var('ps')) {
            throw new SecurityException('Invalid secret');
        }
        
        // headers & readfile()...
        $rev = $sf->getLastRevision();
        $file = get_data_file('/filesync/'.$sf->getStoreId() . '/' . $sf->getStoreFileId() . '-' . $rev->getStoreFileRevId());
        if (!$file) {
            throw new ObjectNotFoundException('File not found');
        }
        
        // log download
        $storeService->logPublicDownload( $sf->getStoreFileId() );
        
        header('Content-type: ' . mime_content_type ($file));
        header('Content-Disposition: '.(get_var('inline')?'inline':'attachment').'; filename="'.$sf->getFilename().'"');
        readfile( $file );
        exit;
        
    }
}

