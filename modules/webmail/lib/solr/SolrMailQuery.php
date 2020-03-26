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
    
    
    public function setMailTabSettings( MailTabSettings $mailtabSettings ) {
        
        $qs = array();
        
        // apply default filter(s)? (e-mailadresses linked to company/person)
        
        $filters = $mailtabSettings->getFilters();
        
        if ($mailtabSettings->applyDefaultFilters()) {
            $defaultFilters = $mailtabSettings->getDefaultFilters();
            $filters = array_merge($filters, $defaultFilters);
        }
        
        // other filters specified?
        foreach($filters as $filter) {
            if ($filter['filter_type'] == 'email') {
                $v = solr_escapeTerm( trim($filter['filter_value']) );
                // unescape asterisks
                $v = str_replace('\\*', '*', $v);
                
                // @domainname.com? => prefix with asterisk
                if (strpos($v, '@') === 0) {
                    $v = '*'.$v;
                }
                
                $qs[] = 'toEmail:'.$v;
                $qs[] = 'fromEmail:'.$v;
            }
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


