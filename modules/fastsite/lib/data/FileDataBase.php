<?php


namespace fastsite\data;


use core\Context;
use core\exception\FileException;
use core\exception\ResourceException;
use core\exception\InvalidStateException;

class FileDataBase {

    protected $data = array();
    
    
    
    public function getTemplatesDir() {
        $path = get_data_file('fastsite/templates');
        
        // false? => try to create
        if ($path == false) {
            $ctx = Context::getInstance();
            
            $datadir = realpath($ctx->getDataDir());
            
            if (mkdir($datadir . '/fastsite/templates', 0755, true) == false) {
                throw new FileException('Unable to create templates-directory');
            }
            
            $path = get_data_file('fastsite/templates');
        }
        
        
        return $path;
    }
    
    
    
    public function setValue($key, $val) { $this->data[$key] = $val; }
    public function getValue($key, $defaultValue=null) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        
        return $defaultValue;
    }
    
    
    public function save($path) {

        $templatesDir = $this->getTemplatesDir();
        $fullpath = $this->getTemplatesDir() . '/' . $path;
        
        $dir = dirname($fullpath);
        if (strpos($dir, $templatesDir) !== 0) {
            throw new ResourceException('File outside template dir requested');
        }
        
        if (file_exists($dir) == false) {
            mkdir($dir, 0755, true);
        }
        
        $data = serialize($this->data);
        
        $r = file_put_contents( $fullpath, $data );
        
        return $r !== false;
    }
    
    public function load($path) {
        
        $templatesDir = $this->getTemplatesDir();
        $fullpath = realpath($templatesDir . '/' . $path);
        
        // doesn't exist?
        if ($fullpath === false) {
            return false;
        }
        
        if (strpos($fullpath, $templatesDir) === false) {
            throw new ResourceException('Invalid location');
        }
        
        if (file_exists($fullpath)) {
            $data = @unserialize( file_get_contents( $fullpath ));
            if ($data) {
                $this->data = $data;
                return true;
            }
        }
        
        return false;
    }
    
    
    
}
