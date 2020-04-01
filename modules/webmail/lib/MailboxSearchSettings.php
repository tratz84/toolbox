<?php


namespace webmail;

use base\model\User;
use webmail\solr\SolrMailQuery;



class MailboxSearchSettings {
    
    protected $userId;
    
    protected $data = null;
    
    protected $cache_defaultFilters = null;
    
    
    public function __construct($userId=null) {
        if ($userId == null) {
            $userId = ctx()->getUser()->getUserId();
        }
        $this->userId = $userId;
        
        $this->load();
    }
    
    
    protected function load() {
        $this->data = object_meta_get(User::class, $this->userId, 'mailbox-search-settings');
        
        if (is_array($this->data) == false) {
            $this->data = array();
        }
            
        
        if (isset($this->data['includeFilters']) == false || is_array($this->data['includeFilters']) == false) {
            $this->data['includeFilters'] = array();
        }
        
        if (isset($this->data['excludeFilters']) == false || is_array($this->data['excludeFilters']) == false) {
            $this->data['excludeFilters'] = array();
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
    
    public function applyFilters(SolrMailQuery $smq) {
        
        $qs_inc = array();
        
        $includeFilters = $this->getIncludeFilters();
        foreach($includeFilters as $filter) {
            if ($filter['filter_type'] == 'email') {
                $v = solr_escapeTerm( trim($filter['filter_value']) );
                // unescape asterisks
                $v = str_replace('\\*', '*', $v);
                
                // @domainname.com? => prefix with asterisk
                if (strpos($v, '@') === 0) {
                    $v = '*'.$v;
                }
                
                $qs_inc[] = 'toEmail:'.$v;
                $qs_inc[] = 'fromEmail:'.$v;
            }
            
            if ($filter['filter_type'] == 'folder') {
                $v = solr_escapePhrase( trim($filter['filter_value']) );
                
                $qs_inc[] = 'mailboxName:'.$v;
            }
        }
        

        // exclude through faces
        $excludeFilters = $this->getExcludeFilters();
        foreach($excludeFilters as $filter) {
            if ($filter['filter_type'] == 'email') {
                $v = solr_escapeTerm( trim($filter['filter_value']) );
                // unescape asterisks
                $v = str_replace('\\*', '*', $v);
                
                // @domainname.com? => prefix with asterisk
                if (strpos($v, '@') === 0) {
                    $v = '*'.$v;
                }
                
                $smq->addFacetQuery("-toEmail", ':', $v);
                $smq->addFacetQuery("-fromEmail", ':', $v);
            }
            
            if ($filter['filter_type'] == 'folder') {
                $v = solr_escapePhrase( trim($filter['filter_value']) );
                
                $smq->addFacetQuery('-mailboxName:'.$v);
            }
        }
        
        
        // append includes to current query
        if (count($qs_inc)) {
            $q = '(' . implode(' OR ', $qs_inc) . ')';
            if ($this->query != '*:*') {
                $q .= ' AND ( ' . $this->query . ')';
            }
            $this->setRawQuery( $q );
        }
        
    }
    
    
}
