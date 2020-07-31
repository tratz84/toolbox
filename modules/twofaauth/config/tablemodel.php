<?php


use core\db\TableModel;

$tbs = array();


$tb_fa = new TableModel('twofaauth', 'two_fa_cookie');
$tb_fa->addColumn('cookie_id',    'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_fa->addColumn('cookie_value', 'varchar(255)');
$tb_fa->addColumn('secret_key',   'varchar(32)');
$tb_fa->addColumn('activated',    'boolean');
$tb_fa->addColumn('last_visit',   'datetime');
$tb_fa->addColumn('created',      'datetime');
$tb_fa->addIndex('index_value', ['cookie_value']);
$tbs[] = $tb_fa;



return $tbs;

