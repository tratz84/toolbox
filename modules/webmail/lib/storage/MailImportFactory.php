<?php


namespace webmail\storage;


use webmail\solr\SolrImportMail;

class MailImportFactory {
    
    
    
    public static function getImportMail() {
        if (webmail_storage_engine() == 'solr') {
            $si = new SolrImportMail();
            $si->setSolrUrl( 'WEBMAIL_SOLR' );
            return $si;
        }
        else {
            return new MysqlImportMail();
        }
    }
    
    
}


