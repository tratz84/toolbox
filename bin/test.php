#!/usr/bin/php
<?php

include dirname(__FILE__).'/../config/config.php';


// parse mail, /home/timvw/projects/toolbox/data/dev/email/inbox/2020/02/27/9b1ac03051ce57e4ef0aea70e447a12c

$parser = new PhpMimeMailParser\Parser();
$parser->setPath( '/home/timvw/projects/toolbox/data/dev/email/inbox/2020/02/27/9b1ac03051ce57e4ef0aea70e447a12c' );

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




// $t = new \base\model\User(5);
// $t->read();

// $t->setUsername('timbo123');
// $t->save();
// use base\model\UserDAO;

// $uDao = new UserDAO();
// $users = $uDao->queryCursor("select * from base__user");


// var_export($users->numRows());

