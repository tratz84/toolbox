<?php


namespace webmail;

use base\model\User;
use webmail\solr\SolrMailQuery;



class MailboxSearchSettings {
    
    protected $userId;
    
    protected $data = null;
    
    protected $cache_defaultFilters = null;
    
    
    public function __construct($userId=null, $opts=array()) {
        if ($userId == null) {
            $userId = ctx()->getUser()->getUserId();
        }
        $this->userId = $userId;
        
        if (isset($opts['data'])) {
            $this->load( $opts['data'] );
        } else {
            $this->load();
        }
    }
    
    
    protected function load( $data = null) {
        if ($data === null) {
            $this->data = object_meta_get(User::class, $this->userId, 'mailbox-search-settings');
        } else {
            $this->data = $data;
        }
        
        if (is_array($this->data) == false) {
            $this->data = array();
        }
            
        
        if (isset($this->data['includeFilters']) == false || is_array($this->data['includeFilters']) == false) {
            $this->data['includeFilters'] = array();
        }
        
        if (isset($this->data['excludeFilters']) == false || is_array($this->data['excludeFilters']) == false) {
            $this->data['excludeFilters'] = array();
        }
        
        if (isset($this->data['hideFolderList']) == false || is_array($this->data['hideFolderList']) == false) {
            $this->data['hideFolderList'] = array();
        }
    }
    
    public function save() {
        return object_meta_save(User::class, $this->userId, 'mailbox-search-settings', $this->data);
    }
    
    public function getData() { return $this->data; }
    
    
    
    public function clearIncludeFilters() { $this->data['includeFilters'] = array(); }
    public function getIncludeFilters() { return $this->data['includeFilters']; }
    public function addIncludeFilter($filter_type, $filter_value) {
        $this->data['includeFilters'][] = array(
            'filter_type' => $filter_type,
            'filter_value' => $filter_value
        );
    }
    

    public function clearExcludeFilters() { $this->data['excludeFilters'] = array(); }
    public function getExcludeFilters() { return $this->data['excludeFilters']; }
    public function addExcludeFilter($filter_type, $filter_value) {
        $this->data['excludeFilters'][] = array(
            'filter_type' => $filter_type,
            'filter_value' => $filter_value
        );
    }

    public function clearHideFolders() { $this->data['hideFolderList'] = array(); }
    public function getHideFolders() { return $this->data['hideFolderList']; }
    public function addHideFolders($folderName) {
        $this->data['hideFolderList'][] = array(
            'folder_name' => trim( $folderName )
        );
    }
    public function getHideFolderNameList() {
        $l = array();
        foreach($this->data['hideFolderList'] as $f) {
            $l[] = $f['folder_name'];
        }
        return $l;
    }
    
    
    
}
