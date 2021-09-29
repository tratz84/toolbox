<?php



use core\db\TableModel;


$tbs = array();


$tb_l = new TableModel('fail2ban', 'list');
$tb_l->addColumn('whitelist_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_l->addColumn('ip',           'varchar(64)');
$tb_l->addColumn('note',         'text');
$tb_l->addColumn('type',         "enum('allow','block')");
$tb_l->addColumn('sort',         'int');
$tb_l->addColumn('active',       'bool');
$tb_l->addColumn('edited',       'datetime');
$tb_l->addColumn('created',      'datetime');
$tbs[] = $tb_l;


$tb_check =new TableModel('fail2ban', 'check');
$tb_check->addColumn('abuse_check_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_check->addColumn('ip',              'varchar(64)');
$tb_check->addColumn('message',         'varchar(64)');
$tb_check->addColumn('created',         'datetime');
$tbs[] = $tb_check;


return $tbs;
