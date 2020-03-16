<?php

namespace core\db\solr;

class SolrQueryResponse {
    
    protected $solrQuery = null;
    protected $solrResponseText;
    
    protected $json = null;
    
    
    public function __construct($solrResponseText, $solrQuery=null) {
        $this->solrResponseText = $solrResponseText;
        $this->solrQuery = $solrQuery;
    }
    
    protected function parse() {
        $this->json = @json_decode( $this->solrResponseText );
    }
    
    
    
}

