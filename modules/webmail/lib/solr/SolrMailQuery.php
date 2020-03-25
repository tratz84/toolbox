<?php

namespace webmail\solr;


use core\db\solr\SolrQuery;
use core\forms\lists\ListResponse;

class SolrMailQuery extends SolrQuery {
    
    
    public function __construct() {
        parent::__construct( WEBMAIL_SOLR );
        
        $this->responseClass = SolrMailQueryResponse::class;
        
        $ctx = \core\Context::getInstance();
        
        $this->addFacetSearch('contextName', ':', $ctx->getContextName());
    }
    
    
    
    public function searchListResponse() {
        if (!$this->getSort()) {
            if ($this->query == '*:*') {
                $this->setSort('date desc');
            } else {
                $this->setSort('score desc, date desc');
            }
            
        }
        
        
        /** @var SolrMailQueryResponse $msqr */
        $msqr = $this->search();
        
        $mails = array();
        for($x=0; $x < $msqr->getRows(); $x++) {
            /** @var SolrMail $mail */
            $mail = $msqr->getMail( $x );
            
            $mh = array();
            $mh['email_id']     = $mail->getId();
            $mh['mailbox_name'] = $mail->getMailboxName();
            $mh['subject']      = $mail->getSubject();
            $mh['date']         = $mail->getDate();
            $mh['from_name']    = $mail->getFromName();
            $mh['from_email']   = $mail->getFromEmail();
            
            $mails[] = $mh;
        }
        
        $lr = new ListResponse($msqr->getStart(), $msqr->getRows(), $msqr->getNumFound(), $mails);
        
        return $lr;
    }
    
    
}


