#!/usr/bin/php
<?php

include dirname(__FILE__).'/../config/config.php';


bootstrapCli('dev');


// parse mail, /home/timvw/projects/toolbox/data/dev/webmail/inbox/2020/02/27/9b1ac03051ce57e4ef0aea70e447a12c

$parser = new PhpMimeMailParser\Parser();
$parser->setPath( '/home/timvw/projects/toolbox/data/dev/webmail/inbox/2020/02/27/9b1ac03051ce57e4ef0aea70e447a12c' );

$html = $parser->getMessageBody('html');
// $text = $parser->getMessageBody('text');

// print $html;exit;
$p = new \core\parser\HtmlParser();
$p->loadString( $html );
$p->parse();

$b = $p->getBodyText();
// var_export($b);exit;
print $b;exit;

// var_export($p->getBlocks());

// print $text;



