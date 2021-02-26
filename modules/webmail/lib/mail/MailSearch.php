<?php

namespace webmail\mail;

use core\Context;
use core\forms\lists\ListResponse;
use core\exception\SolrException;

class MailSearch {
    
    protected $solrUrl;
    protected $contextName;
    
    protected $facetQueries = array();
    protected $fields = array('id','contextName','file','date','subject','fromName','fromEmail','toName','toEmail');
    protected $query = '';
    protected $queryOr = array();
    protected $queryAnd = array();
    protected $sort = 'date desc';
    
    protected $start=0;
    protected $pageSize = 5;
    
    
    public function __construct($solrUrl=null, $contextName=null) {
        
        if ($solrUrl) {
            $this->solrUrl = $solrUrl;
        } else {
            $this->solrUrl = WEBMAIL_SOLR;
        }
        
        if ($contextName) {
            $this->contextName = $contextName;
        } else {
            $this->contextName = Context::getInstance()->getContextName();
        }
        
        $this->pageSize = Context::getInstance()->getPageSize();
        
        $this->addFacetQuery('contextName', $this->contextName);
    }
    
    
    public function addFacetQuery($key, $val) {
        $this->facetQueries[] = array($key, $val);
    }
    
    public function setQuery($q) { $this->query = $q; }
    
    
    public function applySearchOptions($opts) {
        
        if (isset($opts['fromName']) && $opts['fromName']) {
            $this->queryAnd[] = 'fromName~'.solr_escapePhrase($opts['fromName']) . ' OR ' . 'fromEmail~'.solr_escapePhrase($opts['fromName']);
        }

        if (isset($opts['subject']) && $opts['subject']) {
            $this->queryAnd[] = solr_escapeTerm($opts['subject']);
        }
        
    }
    
    
    
    public function search($start, $limit, $opts) {
        
        $this->start = $start;
        $this->pageSize = $limit;
        
        $this->applySearchOptions( $opts );
        
        $u = $this->buildUrl();
        
        $response = get_url( $u );
        
        if ($response === false) {
            throw new SolrException('No response from solr server');
        }
        
        $json = json_decode($response);
        
        if ($json === false) {
            throw new SolrException('Invalid response from solr server');
        }
        
        if ($json->responseHeader->status != 0) {
            throw new SolrException( 'Solr error: ' . $json->error->msg );
        }
        
        
        $objects = array();
        foreach($json->response->docs as $d) {
            $date = new \DateTime($d->date);
            $date->setTimezone(new \DateTimeZone('Europe/Amsterdam'));
            
            $objects[] = array(
                'id'          => $d->id,
                'contextName' => $d->contextName,
                'file'        => $d->file,
                'date'        => $date->format('Y-m-d H:i:s'),
                'subject'     => $d->subject,
                'fromName'    => isset($d->fromName) ? $d->fromName : null,
                'fromEmail'   => isset($d->fromName) ? $d->fromEmail : null,
                'toName'      => isset($d->toName)   ? $d->toName  : array(),
                'toEmail'     => isset($d->toEmail)  ? $d->toEmail : array(),
            );
        }
        
        
        $lr = new ListResponse($json->response->start, $this->pageSize, $json->response->numFound, $objects);
        
        return $lr;
    }
    
    public function buildUrl() {
        $orderByScore = false;
        
        $u = $this->solrUrl . '/query?';
        $params = array();
        
        foreach($this->facetQueries as $fq) {
            $params[] = 'fq='.urlencode($fq[0] . ':' . $fq[1]);
        }
        
        if (count($this->fields)) {
            $params[] = 'fl='.urlencode(implode(',', $this->fields));
        }
        
        $params[] = 'rows='.intval($this->pageSize);
        
        
        if (count($this->queryAnd)) {
            $q = implode(' AND ', $this->queryAnd);
            
            $params[] = 'q='.urlencode( $q );
            
            $orderByScore = true;
        } else {
            $params[] = 'q='.urlencode($this->query?$this->query:'*:*');
        }
        
        
        if ($orderByScore) {
            $this->sort = 'score desc, date desc';
        }
        
        if ($this->sort) {
            $params[] = 'sort='.urlencode($this->sort);
        }
        
        $params[] = 'start='.urlencode($this->start);
        
        $url = $u . implode('&', $params);
        
//         print $url;exit;
        
        return $url;
    }
    
    
}

