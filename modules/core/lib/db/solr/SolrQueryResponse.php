<?php

namespace core\db\solr;

use core\exception\InvalidArgumentException;

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
    
    public function hasError() {
        if ($this->response === false || $this->response === null) {
            return true;
        }
        
        return false;
    }
    public function getError() {
        if ($this->response === false || $this->response === null) {
            return 'No valid response (solr down?)';
        }
    }
    
    
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
        return isset($this->response->response->numFound) ? $this->response->response->numFound: 0;
    }
    
    /**
     * getStart() - position first document
     */
    public function getStart() {
        return isset($this->response->response->start) ? $this->response->response->start : 0;
    }
    
    /**
     * getRows() - number of documents in current response
     */
    public function getRows() {
        return isset($this->response->response->docs) ? count( $this->response->response->docs ) : 0;
    }
    
    
    public function getDocument($no) {
        if (isset($this->response->response->docs) && $no >= 0 && $no < count($this->response->response->docs)) {
            return $this->response->response->docs[$no];
        }
        
        throw new InvalidArgumentException('Document not found');
    }
    
    
    public function getDocuments() {
        return isset($this->response->response->docs) ? $this->response->response->docs : array();
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
    
    
    
    public function getFacetField($fieldName) {
        $fields = array();
        
        if (isset($this->response->facet_counts->facet_fields->{$fieldName})) {
            $fr = $this->response->facet_counts->facet_fields->{$fieldName};
            
            $c = count($fr);
            for($x=0; $x < $c; $x+=2) {
                $fields[] = array(
                    'name' => $fr[$x],
                    'value' => $fr[$x+1]
                );
            }
        }
        
        return $fields;
    }
    
    
    
}

