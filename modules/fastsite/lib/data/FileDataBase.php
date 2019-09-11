<?php


namespace fastsite\data;


use core\Context;
use core\exception\FileException;

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
        
        
        $data = serialize($this->data);
        
        $r = file_put_contents( $path, $data );
        
        return $r !== false;
    }
    
    public function load() {
        $d = $this->getTemplatedir() . '/fastsite';
        $filename = slugify($this->filename);
        
        $p = $d . '/page-' . $filename;
        if (file_exists($p)) {
            $data = @unserialize( file_get_contents( $p ));
            if ($data) {
                $this->data = $data;
                return true;
            }
        }
        
        return false;
    }
    
    
    
}
