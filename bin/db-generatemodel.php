#!/usr/bin/env php
<?php

include dirname(__FILE__).'/../config/config.php';

if (count($argv) < 3) {
    print "Usage: {$argv[0]} <database-resource> <module> <(optional start of table-names to import)>\n";
    exit;
}


$resource = $argv[1];
$module = $argv[2];
$startTableNames = null;
if (count($argv) >= 4) {
    $startTableNames = $argv[3];
}


$tables = queryListAsArray($resource, 'show tables');

foreach($tables as $t) {
    // skip?
    if ($startTableNames !== null && strpos($t[0], $startTableNames) !== 0) continue;

    // fetch columns
    $columns = queryList($resource, 'describe '.$t[0]);
    
    // generate
    print "Generating model for {$t[0]}\n";
    $gen = new \core\generator\DAOGenerator($resource, $module, $t[0], $columns);
    $gen->generate();
}




