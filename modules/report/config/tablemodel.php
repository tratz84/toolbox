<?php



use core\db\TableModel;

$tbs = array();



$tb_d = new TableModel('report', 'report_dashboard');
$tb_d->addColumn( 'report_dashboard_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_d->addColumn( 'name',     'varchar(255)' );
$tb_d->addColumn( 'active',   'boolean' );
$tb_d->addColumn( 'settings', 'longtext' );
$tb_d->addColumn( 'css',      'longtext' );
$tb_d->addColumn( 'edited',   'datetime' );
$tb_d->addColumn( 'created',  'datetime' );
$tbs[] = $tb_d;



return $tbs;

