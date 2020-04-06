<?php

namespace webmail\solr;


use core\db\solr\SolrQuery;
use core\forms\lists\ListResponse;
use webmail\MailTabSettings;

class SolrMailQuery extends SolrQuery {
    
    
    public function __construct() {
        parent::__construct( WEBMAIL_SOLR );
        
        $this->responseClass = SolrMailQueryResponse::class;
        
        $ctx = \core\Context::getInstance();
        
        $this->setSort('date desc');
        $this->addFacetSearch('contextName', ':', $ctx->getContextName());
    }
    
    
    public function clearFacetQueries() {
        $this->facetQueries = array();
        
        // MUST have
        $this->addFacetSearch('contextName', ':', ctx()->getContextName());
    }
    
    
    /**
     * 
     * @return SolrMail
     */
    public static function readStaticById($id) {
        $smq = new SolrMailQuery();
        return $smq->readById($id);
    }

    public function readById($id) {
        // reset query
        $this->clearFields();
        $this->clearFacetQueries();
        $this->setRawQuery('*:*');

        // add id-facet
        $this->addFacetSearch('id', ':', $id);
        
        // search
        /** @var SolrMailQueryResponse $smqr */
        $smqr = $this->search();
        
        
        if ($smqr->getNumFound() == 0) {
            return null;
        }
        
        return $smqr->getMail(0);
    }
    
    
    public function searchListResponse() {
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
            $mh['action']       = $mail->getAction();
            
            $mh['answered']     = $mail->isAnswered();
            $mh['seen']         = $mail->isSeen();
            
            if (strtolower($mail->getMailboxName()) == 'junk') {
                $mh['junk'] = true;
            } else {
                $mh['junk'] = false;
            }
            
            $mails[] = $mh;
        }
        
        $lr = new ListResponse($msqr->getStart(), $msqr->getRows(), $msqr->getNumFound(), $mails);
        
        return $lr;
    }
    
    
}


