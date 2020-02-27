<?php
/**
 * Router script for /bin/start_dev.php 
 * 
 * Used for standalone development
 * 
 */


$webrootdir = realpath( __DIR__.'/../www' );

$uri = $_SERVER['REQUEST_URI'];
if (strpos($uri, '?') !== false) {
    $uri = substr($uri, 0, strpos($uri, '?'));
}

$file = realpath($webrootdir.'/'.$uri);

if ($file == $webrootdir) {
    include 'index.php';
    exit;
}

// file not exists? => start index.php
if (!$file) {
    include 'start.php';
    exit;
}

// prevent hacks
if ($file && strpos($file, $webrootdir) !== 0) {
    die('Invalid location');
}


$ext = substr($file, strrpos($file, '.'));

if (in_array($ext, array('.css', '.js', '.less', '.png', '.jpg', '.jpeg', '.ttf', '.woff'))) {
    if (in_array($ext, array('.less', '.css'))) {
        header('Content-type: text/css');
    } else {
        header('Content-type: ' . mime_content_type($file));
    }
    readfile($file);
    exit;
}

die('Invalid file requested');



