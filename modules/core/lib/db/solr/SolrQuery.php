<?php

namespace core\db\solr;


class SolrQuery {
    
    protected $solrUrl;
    
    protected $start = 0;
    protected $rows  = 10;
    protected $sort  = null;
    
    protected $timeAllowed = null;      // max time allowed in ms before query is partially returned
    
    protected $fields      = array();   // fields returned
    
    protected $facetQueries = array();
    protected $query        = '*:*';
    
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
    
    public function addField($fieldName) {
        $this->fields[] = $fieldName;
    }
    
    public function addFacetSearch($fieldName, $operator, $value) {
        $fq = solr_escapeTerm($fieldName) . $operator . solr_escapePhrase($value);
        
        $this->addFacetQuery($fq);
    }
    
    public function addFacetQuery($fq) {
        $this->facetQueries[] = $fq;
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
        
        if ($this->query) {
            $url_params[] = 'q='.urlencode( $this->query );
        }
        
        $url = $this->solrUrl . '/select?' . implode('&', $url_params);
        
        $data = get_url($url);
        
        return new $this->responseClass($data, $this);
    }
    
}

