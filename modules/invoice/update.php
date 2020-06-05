<?php


use core\db\DatabaseHandler;

$sql = array();

$sql[] = 'update invoice__to_bill set type="invoice" where ifnull(type, "") = ""';

$dbh = DatabaseHandler::getConnection('default');

foreach($sql as $s) {
//    $dbh->query( $s );
}
