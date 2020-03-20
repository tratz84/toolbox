<?php


namespace webmail\solr;



use core\db\solr\SolrQueryResponse;
use core\exception\OutOfBoundException;

class SolrMailQueryResponse extends SolrQueryResponse {
    
    protected $mails = array();
    
    public function __construct($solrResponseText, $solrQuery=null) {
        parent::__construct($solrResponseText, $solrQuery);
        
        $this->parse();
    }
    
    protected function parse() {
        foreach($this->getDocuments() as $d) {
            $mail = new SolrMail( $d );
            
            $this->mails[] = $mail;
        }
    }
    
    
    
    public function getMail( $mailNo ) {
        if ($mailNo < 0 || $mailNo >= count($this->mails)) {
            throw new OutOfBoundException('Invalid index number');
        }
        
        return $this->mails[ $mailNo ];
    }
    
    
}


