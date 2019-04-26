#!/usr/bin/php
<?php

include dirname(__FILE__).'/../config/config.php';



// $t = new \base\model\User(5);
// $t->read();

// $t->setUsername('timbo123');
// $t->save();

use base\model\UserDAO;

$uDao = new UserDAO();
$users = $uDao->queryCursor("select * from base__user");


var_export($users->numRows());

