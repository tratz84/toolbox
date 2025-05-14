<?php



use core\db\TableModel;

$tbs = array();

$tb_mp = new TableModel('mollie', 'mollie_payment');
$tb_mp->addColumn('mollie_payment_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_mp->addColumn('amount',             'decimal(10,2)');
$tb_mp->addColumn('description',        'varchar(512)');
$tb_mp->addColumn('mollie_id',          'varchar(512)');
$tb_mp->addColumn('mollie_status',      'varchar(32)');
$tb_mp->addColumn('meta',               'mediumtext');
$tb_mp->addColumn('return_url',         'varchar(255)');
$tb_mp->addColumn('uid',                'varchar(255)');
$tb_mp->addColumn('edited',             'datetime');
$tb_mp->addColumn('created',            'datetime');
$tbs[] = $tb_mp;







return $tbs;


