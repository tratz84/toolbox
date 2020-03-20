<?php

namespace core\db\solr;

class SolrQueryResponse {
    
    protected $solrQuery = null;
    protected $solrResponseText;
    
    protected $response = null;
    
    
    public function __construct($solrResponseText, $solrQuery=null) {
        $this->solrResponseText = $solrResponseText;
        $this->solrQuery = $solrQuery;
        
        $this->response = @json_decode( $this->solrResponseText );
    }
    
    
    public function getResponse() {
        return $this->response;
    }
    
    
    public function getQTime() { return $this->response->responseHeader->QTime; }
    
    /**
     * getParams() - search params
     */
    public function getParams() {
        return $this->response->responseHeader->params;
    }
    
    // always '0' ?? TODO: lookup what this value represents
//     public function getStatus() { return $this->response->responseHeader->status; }
    
    
    /**
     * getNumFound() - total records found
     */
    public function getNumFound() {
        return $this->response->response->numFound;
    }
    
    /**
     * getStart() - position first document
     */
    public function getStart() {
        return $this->response->response->start;
    }
    
    /**
     * getRows() - number of documents in current response
     */
    public function getRows() {
        return count( $this->response->response->docs );
    }
    
    
    
    
    public function getDocuments() {
        return $this->response->response->docs;
    }
    
    
    
    /**
     * getPageCount() - number of pages in search result
     * 
     * @return number - 1 ... 99
     */
    public function getPageCount() {
        $rowsPerPage = isset($this->response->responseHeader->params->rows) ? (int)$this->response->responseHeader->params->rows : 10;
        
        return ceil( $this->getNumFound() / $rowsPerPage);
    }
    
    
    /**
     * getPage() - returns requested pageno
     * @param unknown $pageNo
     */
    public function getPage($pageNo) {
        if ($pageNo < 0) {
            return null;
        }
        
        if ($pageNo >= $this->getPageCount()) {
            // TODO: throw error?
        }
        
        $sq = clone $this->solrQuery;
        $sq->setStart( $pageNo * $this->getRows() );
        
        return $sq->search();
    }
    
    
    
    
}

