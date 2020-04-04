#!/usr/bin/php
<?php

/**
 * update_action_all.php - sets 'action' to all mails
 * 
 * 
 */


use webmail\solr\SolrImportMail;

if (count($argv) < 2) {
    print "Usage: {$argv[0]} <contextname> <new status>\n";
    exit;
}

// move to cwd
chdir(__DIR__.'/../../..');

// bootstrap
include 'config/config.php';
$contextName = $argv[1];
bootstrapCli($contextName);

ini_set('memory_limit', '2GB');


print "START Folder import, importing webmail/inbox\n";
$solrImportMail = new SolrImportMail(WEBMAIL_SOLR);
$solrImportMail->setForcedAction( $argv[2] );
$solrImportMail->importFolder( ctx()->getDataDir().'/webmail/inbox' );
unset( $solrImportMail );

print "DONE Folder import\n";





