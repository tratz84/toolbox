<?php


use core\db\TableModel;

$tbs = array();

$tb_bl = new TableModel('fail2ban', 'blacklist');
$tb_bl->addColumn('blacklist_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_bl->addcolumn('network_address', 'varchar(128)');
$tb_bl->addcolumn('note', 'text');
$tb_bl->addcolumn('active', 'boolean');
$tb_bl->addColumn('created', 'datetime');

$tbs[] = $tb_bl;


return $tbs;

