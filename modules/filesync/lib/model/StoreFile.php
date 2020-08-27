<?php


namespace filesync\model;


class StoreFile extends base\StoreFileBase {
    
    protected $revisions = array();

    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setDeleted(false);
    }
    

    
    public function getRevisions() { return $this->revisions; }
    public function setRevisions($revs) { $this->revisions = $revs; }
    
    public function getLastRevision() {
        $rv = -1;
        $result = null;
        
        foreach($this->revisions as $r) {
            if ($r->getRev() > $rv) {
                $rv = $r->getRev();
                $result = $r;
            }
        }
        
        return $result;
    }
    
    
    public function getFilename() {
        return basename($this->getPath());
    }
    
    public function getSystemPath() {
        if ($this->getStoreId() && $this->getStoreFileId() && $this->getLastRevision() && $this->getLastRevision()->getStoreFileRevId()) {
            $file = get_data_file('/filesync/'.$this->getStoreId() . '/' . $this->getStoreFileId() . '-' . $this->getLastRevision()->getStoreFileRevId());
            
            if ($file) {
                return $file;
            }
        }
        
        
        return null;
    }
    
    
    public function asArray() {
        $arr = array();
        
        $arr['id'] = $this->getStoreFileId();
        $arr['path'] = $this->getPath();
        if ($this->hasField('md5sum'))
            $arr['md5sum'] = $this->getField('md5sum');
        $arr['rev'] = $this->getField('rev');
        if ($this->hasField('filesize'))
            $arr['filesize'] = $this->getField('filesize');
        $arr['deleted'] = $this->getDeleted() ? true : false;
        $arr['created'] = $this->getCreated();
        $arr['lastmodified'] = $this->getField('lastmodified');
        if (!$arr['lastmodified']) {
            $arr['lastmodified'] = time();
        }
        
        return $arr;
    }
    
}

