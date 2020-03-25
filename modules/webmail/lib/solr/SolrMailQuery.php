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
    
    
    public function setMailTabSettings( $mailtabSettings ) {
        if (isset($mailtabSettings['email']) == false)
            return;
        
        // build query
        $qs = array();
        foreach($mailtabSettings['email'] as $e) {
            $e = trim($e);
            
            if (strpos($e, '@') === 0) {
                $e = '*'.$e;
            }
            
            $qs[] = 'toEmail:'.solr_escapeTerm($e);
            $qs[] = 'fromEmail:'.solr_escapeTerm($e);
        }
        
        
        // append query to current query
        if (count($qs)) {
            $q = '(' . implode(' OR ', $qs) . ')';
            if ($this->query != '*:*') {
                $q .= ' AND ( ' . $this->query . ')';
            }
            $this->setRawQuery( $q );
        }
        
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
        
        if ($msqr->hasError()) {
            throw new \core\exception\SolrException( $msqr->getError() );
        }
        
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


