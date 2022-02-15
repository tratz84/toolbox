<?php


namespace webmail\search;

use webmail\MailTabSettings;
use webmail\MailboxSearchSettings;



class SolrMailSearch extends MailSearchBase {
    
    /** @var \webmail\solr\SolrMailQuery */
    protected $smq;
    
    protected $lastResponse = null;
    
    
    public function __construct() {
        parent::__construct();
        
        $this->smq = new \webmail\solr\SolrMailQuery();
    }
    
    
    public function setSort($s) {
        $this->smq->setSort($s);
    }

    public function getFolders( $opts=array() ) {
        return $this->smq->getFolders();
    }
    
    
    public function setStart($s) { $this->smq->setStart( $s ); }
    public function getStart() { return $this->smq->getStart(); }
    
    public function getRows() { return $this->smq->getRows(); }
    public function setRows($r) { $this->smq->setRows( $r ); }
    
    public function setQuery($q) { $this->smq->setQuery( $q ); }
    
    public function addMailboxName($n) { $this->smq->addFacetSearch( 'mailboxName', ':', $n); }
    public function addAction($a) { $this->smq->addFacetSearch( 'action', ':', $a); }
    
    
    public function searchListResponse() {
        $this->lastResponse = $this->smq->searchListResponse();
        
        return $this->lastResponse;
    }
    
    
    public function applyMailTabSettings(MailTabSettings $mts) {
        $qs = array();
        
        // apply default filter(s)? (e-mailadresses linked to company/person)
        
        $filters = $mts->getFilters();
        
        if ($mts->applyDefaultFilters()) {
            $defaultFilters = $mts->getDefaultFilters();
            $filters = array_merge($filters, $defaultFilters);
        }
        
        // other filters specified?
        foreach($filters as $filter) {
            if ($filter['filter_type'] == 'email') {
                $v = solr_escapeTerm( trim($filter['filter_value']) );
                // unescape asterisks
                $v = str_replace('\\*', '*', $v);
                
                // @domainname.com? => prefix with asterisk
                if (strpos($v, '@') === 0) {
                    $v = '*'.$v;
                }
                
                $qs[] = 'toEmail:'.$v;
                $qs[] = 'fromEmail:'.$v;
            }
            
            if ($filter['filter_type'] == 'folder') {
                $v = solr_escapePhrase( trim($filter['filter_value']) );
                
                $qs[] = 'mailboxName:'.$v;
            }
            
        }
        
        // append query to current query
        if (count($qs)) {
            $q = '(' . implode(' OR ', $qs) . ')';
            if ($this->smq->getRawQuery() != '*:*') {
                $q .= ' AND ( ' . $this->smq->getRawQuery() . ')';
            }
            $this->smq->setRawQuery( $q );
        }
    }
    
    
    
    public function applyMailboxSearchSettings( MailboxSearchSettings $mss ) {
        $qs_inc = array();
        
        $includeFilters = $mss->getIncludeFilters();
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
            
            if ($filter['filter_type'] == 'action') {
                $v = solr_escapePhrase( trim($filter['filter_value']) );
                
                $qs_inc[] = 'action:'.$v;
            }
        }
        
        
        // exclude through facets
        $excludeFilters = $mss->getExcludeFilters();
        foreach($excludeFilters as $filter) {
            if ($filter['filter_type'] == 'email') {
                $v = solr_escapeTerm( trim($filter['filter_value']) );
                // unescape asterisks
                $v = str_replace('\\*', '*', $v);
                
                // @domainname.com? => prefix with asterisk
                if (strpos($v, '@') === 0) {
                    $v = '*'.$v;
                }
                
                $this->addFacetQuery("-toEmail", ':', $v);
                $this->addFacetQuery("-fromEmail", ':', $v);
            }
            
            if ($filter['filter_type'] == 'folder') {
                $v = solr_escapePhrase( trim($filter['filter_value']) );
                
                // query specific on mailbox? => skip exclusion
                $skip = false;
                foreach($this->smq->getFacetQueries() as $fq) {
                    if (endsWith($fq, 'mailboxName:'.$v)) {
                        $skip = true;
                        break;
                    }
                }
                
                if ($skip)
                    continue;
                    
                $this->smq->addFacetQuery('-mailboxName:'.$v);
            }
            
            if ($filter['filter_type'] == 'action') {
                $v = solr_escapePhrase( trim($filter['filter_value']) );
                
                $this->smq->addFacetQuery('-action:'.$v);
            }
            
        }
        
        
        // append includes to current query
        if (count($qs_inc)) {
            $query = $this->smq->getRawQuery();
            
            $q = '(' . implode(' OR ', $qs_inc) . ')';
            if ($query != '*:*') {
                $q .= ' AND ( ' . $query . ')';
            }
            $this->smq->setRawQuery( $q );
        }
    }
    
    
    
    public function readById($id) {
        
        return $this->smq->readById($id);
        
    }

}

