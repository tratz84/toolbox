<?php


use core\db\DatabaseHandler;

$sql = array();

$sql[] = 'alter table invoice__to_bill change column type type varchar(16)';
$sql[] = 'update invoice__to_bill set type=\'debet\' where type=\'invoice\'';
$sql[] = 'update invoice__to_bill set type=\'credit\' where type=\'bill\'';


$dbh = DatabaseHandler::getConnection('default');

foreach($sql as $s) {
   $dbh->query( $s );
}
