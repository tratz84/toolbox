<?php

/**
 * 
 * 
 * 
 * TODO: Share URL ?
 * 
 */


use core\controller\BaseController;
use core\exception\NotImplementedException;
use filesync\FilesyncSettings;
use filesync\wopi\WopiStoreFile;

class wopiController extends BaseController {
    
    protected $access_token;
    protected $storeId;
    protected $fileId;
    
    /** @var \filesync\model\StoreFile $storeFile */
    protected $storeFile;
    
    
    public function action_index() {
        // check if WOPI is activated
        $filesyncSettings = object_container_get( FilesyncSettings::class );
        if ( $filesyncSettings->getWopiActive() == false ) {
            header('HTTP/1.1 500 Internal server error');
            print "WOPI not activated";
            return false;
        }
        
        
        // handle request
        $uri = substr( request_uri_no_params(), strlen(appUrl('/filesync/wopi/')) );
        
        $parts = explode('/', $uri);

        $type = $parts[0];
        
        if ($type == 'storefile') {
            $wsf = new WopiStoreFile();
            $wsf->execute();
        }
        else {
            throw new NotImplementedException('Unknown backend type');
        }
        
    }

    
}



