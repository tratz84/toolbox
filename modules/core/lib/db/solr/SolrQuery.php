<?php

namespace core\db\solr;


use core\exception\InvalidArgumentException;

class SolrQuery {
    
    protected $solrUrl;
    
    protected $start = 0;
    protected $rows  = 10;
    protected $sort  = null;
    
    protected $timeAllowed = null;      // max time allowed in ms before query is partially returned
    
    protected $fields      = array();   // fields returned
    
    protected $facetQueries = array();
    protected $query        = '*:*';
    
    protected $facetFields = array();
    protected $facetLimit = -1;
    
    protected $responseClass = SolrQueryResponse::class;
    
    
    public function __construct($solrUrl) {
        $this->solrUrl = $solrUrl;
    }
    
    public function getSolrUrl() { return $this->solrUrl; }
    public function setSolrUrl($u) { $this->solrUrl = $u; }
    
    public function setStart($s) { $this->start = (int)$s; }
    public function getStart() { return $this->start; }
    
    public function setRows($r) { $this->rows = $r; }
    public function getRows() { return $this->rows; }
    
    public function setSort($s) { $this->sort = $s; }
    public function getSort() { return $this->sort; }
    
    public function setTimeAllowed($ms) { $this->timeAllowed = $ms; }
    public function getTimeAllowed() { return $this->timeAllowed; }
    
    public function setQuery($q) { $this->query = solr_escapeTerm($q); }
    public function getRawQuery() { return $this->query; }
    public function setRawQuery($q) { $this->query = $q; }
    
    public function clearFields() { return $this->fields = array(); }
    public function getFields() { return $this->fields; }
    public function addField($fieldName) {
        $this->fields[] = $fieldName;
    }
    
    public function addFacetSearch($fieldName, $operator, $value) {
        $fq = solr_escapeTerm($fieldName) . $operator . solr_escapePhrase($value);
        
        $this->addFacetQuery($fq);
    }
    
    public function getFacetQueries() { return $this->facetQueries; }
    public function clearFacetQueries() { $this->facetQueries = array(); }
    public function addFacetQuery($fq) {
        $this->facetQueries[] = $fq;
    }
    
    
    public function setFacetLimit($l) {
        if (is_numeric($l) == false) {
            throw new InvalidArgumentException('Invalid limit');
        }
        $this->facetLimit = $l;
    }
    public function clearFacetFields() { $this->facetFields = array(); }
    public function addFacetField($fieldName, $opts=array()) {
        $field = array();
        $field['name'] = $fieldName;
        
        // TODO: handle $opts? it may contain properties like ex-facetqueries for facet counting (TODO, implement)
        
        $this->facetFields[] = $field;
    }
    
    
    public function search() {
        $url_params = array();
        
        if ($this->start) {
            $url_params[] = 'start='.intval($this->start);
        }
        $url_params[] = 'rows='.intval($this->rows);
        
        if ($this->sort) {
            $url_params[] = 'sort='.urlencode($this->sort);
        }
        if ($this->timeAllowed) {
            $url_params[] = 'timeAllowed='.urlencode($this->timeAllowed);
        }
        if (count($this->fields)) {
            $url_params[] = 'fl='.urlencode(implode(',', $this->fields));
        }
        foreach($this->facetQueries as $fq) {
            $url_params[] = 'fq='.urlencode( $fq );
        }
        
        if (count($this->facetFields)) {
            $url_params[] = 'facet=true';
            $url_params[] = 'facet.limit=' . $this->facetLimit;
            
            foreach($this->facetFields as $ff) {
                $url_params[] = 'facet.field='.urlencode($ff['name']);
            }
        }
        
        if ($this->query) {
            $url_params[] = 'q='.urlencode( $this->query );
        }
        
        $url = $this->solrUrl . '/select?' . implode('&', $url_params);
        
//         print $url;exit;
        
        $data = get_url($url);
        
        return new $this->responseClass($data, $this);
    }
    
    public function commit() {
        $url = $this->solrUrl . '/update?commit=true';
        
        $data = get_url($url);
        
        $json = @json_decode( $data );
        
        if ($json && isset($json->responseHeader->status) && $json->responseHeader->status == 0) {
            return true;
        } else {
            return false;
        }
    }
    
}


