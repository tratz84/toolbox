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
bootstrapContext($contextName);

$ctx = \core\Context::getInstance();



$su = new SolrUpdate();
$su->truncate();
$su->importFolder($ctx->getDataDir().'/email/inbox');
$su->commit();
