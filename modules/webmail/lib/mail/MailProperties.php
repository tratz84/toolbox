<?php


namespace webmail\mail;



use core\exception\FileException;

class MailProperties {

    protected $file = null;
    protected $properties = array();

    public function __construct($emlFile=null) {
        if (!$emlFile) {
            // null?
        }
        else if (strpos($emlFile, ctx()->getDataDir()) === 0) {
            $this->file = $emlFile.'.properties';
        } else {
            $f = get_data_file( $emlFile );
            if ($f) {
                $this->file = $f . '.properties';
            }
        }
    }
    
    public function load() {
        if ($this->file && file_exists($this->file)) {
            $data = file_get_contents( $this->file );
            $this->properties = json_decode( $data, true );
            
            return is_array($this->properties) ? true : false;
        } else {
            return false;
        }
    }
    
    public function save() {
        if (!$this->file) {
            throw new FileException('No file set');
        }
        
        return file_put_contents($this->file, json_encode($this->properties));
    }
    
    public function getProperties() { return $this->properties; }
    
    public function setProperty($name, $val) {
        $this->properties[$name] = $val;
    }
    
    public function getProperty($name, $defaultValue=null) {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        } else {
            return $defaultValue;
        }
    }
    
    
    public function getConnectorId() { return $this->getProperty('connectorId'); }
    public function setConnectorId($id) { $this->setProperty('connectorId', $id); }
    
    public function getUid() { return $this->getProperty('uid'); }
    public function setUid($u) { return $this->setProperty('uid', $u); }
    
    public function getFolder() { return $this->getProperty('folder'); }
    public function setFolder($f) { $this->setProperty('folder', $f); }
    
    // set in \webmai\mail\ImapConnection
    public function getConnectorDescription() { return $this->getProperty('connectorDescription'); }
    public function getSubject() { return $this->getProperty('subject'); }
    public function getFrom() { return $this->getProperty('from'); }
    public function getTo() { return $this->getProperty('to'); }
    public function getSize() { return $this->getProperty('size'); }
    public function getMessageId() { return $this->getProperty('message_id'); }
    public function getUDate() { return $this->getProperty('udate'); }
    public function getFlagged() { return $this->getProperty('flagged'); }
    public function getAnswered() { return $this->getProperty('answered'); }
    public function getDeleted() { return $this->getProperty('deleted'); }
    public function getSeen() { return $this->getProperty('seen'); }
    public function getDraft() { return $this->getProperty('draft'); }
    
    
}

