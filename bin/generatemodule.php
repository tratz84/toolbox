#!/usr/bin/php
<?php

include dirname(__FILE__).'/../config/config.php';

if (count($argv) != 2) {
    print "Usage: {$argv[0]} <modulename>\n";
    exit;
}

$modulename = $argv[1];

if (preg_match('/^[a-z_0-9]+$/', $modulename) == false) {
    print "Error: only allowed characters: a-z 0-9 _\n";
    exit;
}

if (file_exists(ROOT . '/modules/'.$modulename)) {
    print "Error: module already exists\n";
    exit;
}


$basepath = ROOT . '/modules/'.$modulename;

// generate base structure of a module
mkdir($basepath);
mkdir($basepath . '/controller');
mkdir($basepath . '/lib');
mkdir($basepath . '/lib/model');
mkdir($basepath . '/templates');

print "Done\n";
