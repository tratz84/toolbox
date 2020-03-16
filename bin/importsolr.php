#!/usr/bin/php
<?php


use webmail\mail\SolrUpdate;

if (count($argv) != 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// move to cwd
chdir(dirname(__FILE__));

// bootstrap
include '../config/config.php';
$contextName = $argv[1];
bootstrapCli($contextName);

$ctx = \core\Context::getInstance();



$su = new SolrUpdate();
// $su->truncate();
// $su->importFolder($ctx->getDataDir().'/email/inbox');

$su->queueFile( '/home/timvw/projects/toolbox/data/dev/email/inbox/2020/02/22/c7689b1bb97e699809b5d707bdcaf57f' );
$su->purge( true );

$su->commit();

