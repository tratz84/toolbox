#!/usr/bin/php
<?php

die('deprecated, use webmail_importall.php <contextName> -u');


use webmail\solr\SolrImportMail;

if (count($argv) != 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// move to root
chdir(__DIR__.'/../../../');

// bootstrap
include 'config/config.php';

if (count($argv) != 2) {
    die("Usage: importsolr.php <environment-name>\n");
}

$contextName = $argv[1];
bootstrapCli($contextName);

ini_set('memory_limit', '2GB');

$ctx = \core\Context::getInstance();


$solrImportMail = new SolrImportMail(WEBMAIL_SOLR);
$solrImportMail->importFolder( $ctx->getDataDir().'/webmail/inbox' );



