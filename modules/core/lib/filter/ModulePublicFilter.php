<?php

namespace core\filter;


use core\exception\InvalidStateException;

class ModulePublicFilter {
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        // support for files by 'mpf'-parameter. Used in cases where rewrites are not working
        if (isset($_GET['mpf'])) {
            $uri = $_GET['mpf'];
        } else {
            try {
                $uri = app_request_uri();
            } catch (InvalidStateException $ex) {
                return $filterChain->next();
            }
        }
        
        // non-module path?
        if (strpos($uri, '/module/') !== 0) {
            return $filterChain->next();
        }
        
        
        $modules = module_list();
        foreach($modules as $moduleName => $path) {
            if (strpos($uri, '/module/'.$moduleName.'/') !== 0)
                continue;
            
            // remove everything after question-mark
            if (strpos($uri, '?') !== false)
                $uri = substr($uri, 0, strpos($uri, '?'));
            
            // determine path in public/-folder
            $publicFolderPath = substr($uri, strlen('/module/'.$moduleName.'/'));
            
            
            // check if module has public folder
            $publicFolder = realpath( $path . '/public/' );
            if ($publicFolder == false)
                $this->return404('No public folder for module');
            
            // determine path
            $fullpath = realpath( $publicFolder.'/'.$publicFolderPath );
            
            // 404 ?
            if ($fullpath == false)
                $this->return404('File not found');
            // outside publicFolder-path?
            if (strpos($fullpath, $publicFolder) !== 0)
                $this->return404('File not found');
            
            
            // set headers
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24))); // 24 hours
            header("Pragma: cache");
            header("Cache-Control: max-age=3600");
            $contentType = file_mime_type($fullpath);
            header('Content-type: '.$contentType);
            
            // output file
            readfile( $fullpath );
            exit;
        }
        
        $this->return404('Module not found');
        
    }
    
    protected function return404($message='404') {
        header('HTTP/1.0 404 Not Found');
        
        die($message);
        
        exit;
    }
    
}

