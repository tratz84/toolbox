<?php


namespace filesync\wopi;


use core\exception\FileException;
use core\exception\InvalidStateException;
use filesync\service\WopiService;

class WopiSystemFile extends WopiBase {
    
    // basePath, for not exposing full path in url
    protected $basePath = '';
    
    // mark doc as writable
    protected $isWritable = true;
    
    protected $wopiToken = null;
    
    public function __construct() {
        
    }
    
    public function setWritable($bln) { $this->isWritable = $bln ? true : false; }
    public function isWritable() { return $this->isWritable; }
    
    public function setBasePath( $p ) { $this->basePath = $p; }
    public function getBasePath() { return $this->basePath; }
    
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
        $this->wopiToken = $token = $wopiService->readTokenById( $id );
        
        // token not found?
        if ($token == null) {
            header('HTTP/1.1 401 Unauthorized');
            print "Token not found";
            return false;
        }
        
        
        // + 300 to give it some slack
        if (date2unix($token->getCreated()) + $token->getAccessTokenTtl() + 300 < time()) {
            header('HTTP/1.1 401 Unauthorized');
            print "Token expired";
            return false;
        }
        
        // check path
        $uri = substr( request_uri_no_params(), strlen(appUrl('/filesync/wopi/systemfile')) );
        
        $fullpath = realpath( $token->getBasePath() . '/' . substr($token->getPath(), strlen('systemfile:')) );
        
        if (endsWith($uri, '/contents')) {
            $uri = substr( $uri, 0, -strlen('/contents') );
        }
        
        if ( realpath($this->wopiToken->getBasePath().'/'.$uri) != $fullpath ) {
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
        
        
        $uri = substr( request_uri_no_params(), strlen(appUrl('/filesync/wopi/systemfile')) );
        
        // validate access_token
        $this->access_token = get_var('access_token');
        
        // determine action
        $action = 'CheckFileInfo';
        
        $last_part = substr( $uri, strlen( substr($this->wopiToken->getPath(), strlen('systemfile:')) ) + 1 );
        
        if ($last_part) {
            $action = $last_part;
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
        
        $path = realpath( $this->wopiToken->getBasePath() . '/' . substr($this->wopiToken->getPath(), strlen('systemfile:')) );
        
        $r['BaseFileName']   = basename($path);
        $r['OwnerId']        = 0;
        $r['Size']           = filesize($path);
        $r['UserId']         = 0;
        $r['Version']        = 1;
        $r['SupportsUpdate'] = true;
        $r['ReadOnly'] = false;
        $r['RestrictedWebViewOnly'] = false;
        
        $r['UserCanWrite'] = $this->isWritable();
        
        // convert to UTC
        $dt = new \DateTime( date('Y-m-d H:i:s', filemtime($path)), new \DateTimeZone(date_default_timezone_get()) );
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
        $path = realpath( $this->wopiToken->getBasePath() . '/' . substr($this->wopiToken->getPath(), strlen('systemfile:')) );
        
        if (!$path || file_exists($path) == false) {
            throw new FileException('File not found');
        }
        
        header('Content-type: ' . mime_content_type ($path));
        header('Content-Disposition: '.(get_var('inline')?'inline':'attachment').'; filename="'.basename($path).'"');
        
        readfile($path);
    }
    
    
    public function handle_PutFile() {
        
        if ($this->isWritable() == false) {
            header('HTTP/1.1 401 Unauthorized');
            print "Not allowed to save non-share store files";
            return false;
        }
        
        $data = file_get_contents('php://input');
        
        $path = realpath( $this->wopiToken->getBasePath() . '/' . substr($this->wopiToken->getPath(), strlen('systemfile:')) );
        
        $r = file_put_contents( $path, $data );
        if ($r === false || $r < 0) {
            header('HTTP/1.1 500 internal server error');
            print "Error saving file";
            return false;
        }
        
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




