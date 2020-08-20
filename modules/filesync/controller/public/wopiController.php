<?php

/**
 * 
 * 
 * 
 * TODO: Share URL ?
 * 
 */


use core\controller\BaseController;
use core\exception\InvalidStateException;
use filesync\service\StoreService;
use core\exception\FileException;
use filesync\exception\StoreFileException;

class wopiController extends BaseController {
    
    protected $access_token;
    protected $storeId;
    protected $fileId;
    
    /** @var \filesync\model\StoreFile $storeFile */
    protected $storeFile;
    
    
    public function action_index() {
        $uri = substr( request_uri_no_params(), strlen(appUrl('/filesync/wopi/')) );
        
        $parts = explode('/', $uri);
        if (count($parts) < 2) {
            throw new InvalidStateException('Invalid url');
        }

        $this->storeId = (int)$parts[0];
        $this->fileId  = (int)$parts[1];
        
        // fetch storefile
        $storeService = object_container_get( StoreService::class );
        $this->storeFile = $storeService->readStoreFile( $this->fileId );
        
        // check if file exists
        if ($this->storeFile == null) {
            throw new StoreFileException('File not found');
        }
        else if ($this->storeFile->getStoreId() != $this->storeId) {
            throw new StoreFileException('Invalid store selected');
        }
        
        // TODO: validate access_token
        $this->access_token = get_var('access_token');
        
        // determine action
        $action = 'CheckFileInfo';
        if (count($parts) >= 3) {
            $action = $parts[2];
        }
        
        if ($action == 'CheckFileInfo') {
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'LOCK') {
                $action = 'Lock';
            }
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'GET_LOCK') {
                $action = 'GetLock';
            }
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'REFRESH_LOCK') {
                $action = 'RefreshLock';
            }
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'UNLOCK') {
                $action = 'Unlock';
            }
            
            // TODO: UnlockAndRelock ?
            
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'PUT') {
                $action = 'PutFile';
            }
            
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'PUT_RELATIVE') {
                // new file..
                $action = 'PutRelative';
            }
            
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'RENAME_FILE') {
                $action = 'RenameFile';
            }

            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'DELETE') {
                $action = 'Delete';
            }
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'GET_SHARE_URL') {
                $action = 'GetShareUrl';
            }
            
            if ($_SERVER['HTTP_X_WOPI_OVERRIDE'] == 'PUT_USER_INFO') {
                $action = 'PutUserInfo';
            }
        }
        
        
        if (method_exists($this, 'handle_'.$action)) {
            $f = 'handle_'.$action;
            $this->$f();
        } else {
            throw new InvalidStateException('Unsupported WOPI operation');
        }
        
        
        
    }

    
    public function handle_CheckFileInfo() {
        $r = array();
        
        $r['BaseFileName']   = $this->storeFile->getFilename();
        $r['OwnerId']        = 0;
        $r['Size']           = $this->storeFile->getLastRevision()->getFilesize();
        $r['UserId']         = 0;
        $r['Version']        = $this->storeFile->getRev();
        $r['SupportsUpdate'] = true;
        $r['ReadOnly'] = false;
        $r['RestrictedWebViewOnly'] = false;
        $r['UserCanWrite'] = true;
        
        $this->json( $r );
    }


    public function handle_contents() {
        if (is_get()) {
            $this->handle_contents_get();
        }
        if (is_post()) {
            $this->handle_contents_post();
        }
    }
    
    public function handle_contents_get() {
        $sf = $this->storeFile;
        $rev = $this->storeFile->getLastRevision();
        
        $file = get_data_file('/filesync/'.$sf->getStoreId() . '/' . $sf->getStoreFileId() . '-' . $rev->getStoreFileRevId());
        
        if (!$file) {
            throw new FileException('File not found');
        }
        
        header('Content-type: ' . mime_content_type ($file));
        header('Content-Disposition: '.(get_var('inline')?'inline':'attachment').'; filename="'.$sf->getFilename().'"');
        
        
        readfile($file);
    }
    
    
    public function handle_PutFile() {
        $data = file_get_contents('php://input');
        
        // TODO: update file..
        
    }
    
    
    
    public function handle_Lock() {
        // lock-id max. length 1024-ascii chars
        // expires after 30 minutes, unless refreshed
        // not associated to a user
        // X-WOPI-Lock-header?
    }
    
    public function handle_RefreshLock() {
    }
    
    public function handle_Unlock() {
    }
    public function handle_UnlockAndRelock() {
    }
    
}



