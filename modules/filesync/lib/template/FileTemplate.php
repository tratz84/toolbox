<?php


namespace filesync\template;


class FileTemplate {
    
    protected $id          = null;
    protected $name        = null;
    protected $description = null;
    
    protected $storeFileId = null;
    protected $file        = null;
    
    protected $vars = array();
    
    public function __construct() {
        
    }
    
    
    public function setId($i) { $this->id = $i; }
    public function getId() { return $this->id; }
    
    public function setName($n) { $this->name = $n; }
    public function getName() { return $this->name; }
    
    public function setDescription($d) { $this->description = $d; }
    public function getDescription() { return $this->description; }
    
    public function getStoreFileId() { return $this->storeFileId; }
    public function setStoreFileId($i) { $this->storeFileId = $i; }
    
    public function getFile() { return $this->file; }
    public function setFile($f) { $this->file = $f; }
    
    public function setVar($name, $opts = array()) {
        $this->vars[$name] = $opts;
    }
    
    public function setVarData($name, $description, $exampleValue) {
        $this->vars[$name] = array(
            'description' => $description,
            'exampleValue' => $exampleValue
        );
    }
    
    public function getVars() { return $this->vars; }
    
    
}

