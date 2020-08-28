<?php


namespace filesync\wopi;


use core\exception\FileException;
use core\exception\InvalidStateException;
use filesync\exception\StoreFileException;
use filesync\service\StoreService;
use filesync\service\WopiService;
use filesync\model\StoreFile;
use filesync\model\Store;

class WopiStoreFile extends WopiBase {
    
    protected $storeId;
    protected $storeFileId;
    
    /** @var Store $store */
    protected $store = null;
    
    /** @var StoreFile $storeFile */
    protected $storeFile = null;
    
    
    public function __construct() {
        
    }
    
    protected function isWritable() {
        if ($this->store->getStoreType() == 'share') {
            return true;
        } else {
            return false;
        }
    }
    
    
    public function validateToken() {
        // get access token
        $token = get_var('access_token');
        $parts = explode(':', $token);
        
        if (count($parts) != 2) {
            throw new InvalidStateException('No valid access token');
        }
        
        $id = $parts[0];
        $token = $parts[1];
        
        $wopiService = object_container_get( WopiService::class );
        $token = $wopiService->readTokenById( $id );
        
        // token not found?
        if ($token == null) {
            header('HTTP/1.1 401 Unauthorized');
            print "Token not found";
            return false;
        }
        
        
        // + 60-sec to give it some slack
        if ($token->getAccessTokenTtl() < (time()*1000) + 60000) {
            header('HTTP/1.1 401 Unauthorized');
            print "Token expired";
            return false;
        }
        
        // check path
        $uri = substr( request_uri_no_params(), strlen(appUrl('/filesync/wopi/')) );
        
        $parts = explode('/', $uri);
        if (count($parts) < 3 || $token->getPath() != $parts[0] . ':/' . $parts[1] . '/' . $parts[2]) {
            header('HTTP/1.1 401 Unauthorized');
            print "No access to requested file";
            return false;
        }
        
        
        return true;
    }
    
    public function execute() {
        // validate access_token
        if ($this->validateToken() == false) {
            return false;
        }
        
        
        $uri = substr( request_uri_no_params(), strlen(appUrl('/filesync/wopi/')) );
        
        $parts = explode('/', $uri);
        
        if (count($parts) < 3) {
            throw new InvalidStateException('Invalid url');
        }
        
        $this->storeId     = $parts[1];
        $this->storeFileId = $parts[2];
        
        
        // fetch storefile
        $storeService = object_container_get( StoreService::class );
        $this->storeFile = $storeService->readStoreFile( $this->storeFileId );
        
        // check if file exists
        if ($this->storeFile == null) {
            throw new StoreFileException('File not found');
        }
        else if ($this->storeFile->getStoreId() != $this->storeId) {
            throw new StoreFileException('Invalid store selected');
        }
        
        // check if system file exists
        $file = $this->storeFile->getSystemPath();
        if ($file == null) {
            header('HTTP/1.1 500 Internal server error');
            print "System file not found";
            return false;
        }
        
        
        // load store
        $this->store = $storeService->readStore( $this->storeId );
        
        // validate access_token
        $this->access_token = get_var('access_token');
        
        // determine action
        $action = 'CheckFileInfo';
        if (count($parts) >= 4) {
            $action = $parts[3];
        }
        
        if (($action == 'CheckFileInfo' || $action == 'contents') && isset($_SERVER['HTTP_X_WOPI_OVERRIDE'])) {
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
        
        // ref @ https://docs.microsoft.com/en-us/openspecs/office_protocols/ms-wopi/a4ba20a7-b571-4ba9-9cac-3f71cac4847a
        
        $r['BaseFileName']   = $this->storeFile->getFilename();
        $r['OwnerId']        = 0;
        $r['Size']           = $this->storeFile->getLastRevision()->getFilesize();
        $r['UserId']         = 0;
        $r['Version']        = $this->storeFile->getRev();
        $r['SupportsUpdate'] = true;
        $r['ReadOnly'] = false;
        $r['RestrictedWebViewOnly'] = false;
        
        $r['UserCanWrite'] = $this->isWritable();
        
        // convert to UTC
        $dt = new \DateTime( $this->storeFile->getEdited(), new \DateTimeZone(date_default_timezone_get()) );
        $dt->setTimezone(new \DateTimeZone('UTC') );
        $r['LastModifiedTime'] = $dt->format('Y-m-d').'T'.$dt->format('H:i:s').'.0000000Z';
        
        // set current TimeZone
        $r['TimeZone'] = 'Europe/Amsterdam';
        
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
        
        $file = $this->storeFile->getSystemPath();
        
        if (!$file) {
            throw new FileException('File not found');
        }
        
        header('Content-type: ' . mime_content_type ($file));
        header('Content-Disposition: '.(get_var('inline')?'inline':'attachment').'; filename="'.$sf->getFilename().'"');
        
        
        readfile($file);
    }
    
    
    public function handle_PutFile() {
        
        if ($this->isWritable() == false) {
            header('HTTP/1.1 401 Unauthorized');
            print "Not allowed to save non-share store files";
            return false;
        }
        
        $data = file_get_contents('php://input');
        
        $tmpfile = '/tmp/wopi-' . md5(uniqid().uniqid().uniqid().uniqid());
        save_data( $tmpfile, $data );
        
        // set fullpath
        $tmpfile = get_data_file( $tmpfile );
        
        $md5sum = md5( $data );
        $filesize = filesize( $tmpfile );
        $lastmodified = date('Y-m-d H:i:s');
        $encrypted = false;
        
        $sf = $this->storeFile;
        
        $storeService = object_container_get( StoreService::class );
        $storeService->syncFile( $sf->getStoreId(), $sf->getPath(), $md5sum, $filesize, $lastmodified, $encrypted, $tmpfile );
        
        unlink( $tmpfile );
        
        print 'OK';
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




