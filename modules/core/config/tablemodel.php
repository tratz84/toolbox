<?php

use core\db\TableModel;

$tbs = array();


$tb_autologin = new TableModel('toolbox', 'autologin', 'admin');
$tb_autologin->addColumn('autologin_id',   'bigint', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_autologin->addColumn('contextName',    'varchar(64)');
$tb_autologin->addColumn('securityString', 'varchar(128)');
$tb_autologin->addColumn('username',       'varchar(128)');
$tb_autologin->addColumn('ip',             'varchar(50)');
$tb_autologin->addColumn('lastUsed',       'datetime');
$tb_autologin->addColumn('created',        'datetime');
$tb_autologin->addIndex('contextName', array('contextName', 'securityString'), ['unique' => true]);
$tbs[] = $tb_autologin;

$tb_customer = new TableModel('toolbox', 'customer', 'admin');
$tb_customer->addColumn('customer_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_customer->addColumn('contextName',  'varchar(64)');
$tb_customer->addColumn('databaseName', 'varchar(64)');
$tb_customer->addColumn('description',  'varchar(255)');
$tb_customer->addColumn('note',         'text');
$tb_customer->addColumn('experimental', 'boolean');
$tb_customer->addColumn('active',       'boolean');
$tb_customer->addIndex('contextName', array('contextName'), ['unique' => true]);
$tb_customer->addIndex('databaseName', array('databaseName'), ['unique' => true]);
$tbs[] = $tb_customer;

$tb_exception_log = new TableModel('toolbox', 'exception_log', 'admin');
$tb_exception_log->addColumn('exception_log_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_exception_log->addColumn('contextName', 'varchar(64)');
$tb_exception_log->addColumn('user_id',     'int');
$tb_exception_log->addColumn('request_uri', 'varchar(255)');
$tb_exception_log->addColumn('message',     'varchar(255)');
$tb_exception_log->addColumn('stacktrace',  'mediumtext');
$tb_exception_log->addColumn('parameters',  'mediumtext');
$tb_exception_log->addColumn('created',     'datetime');
$tbs[] = $tb_exception_log;

$tb_user = new TableModel('toolbox', 'user', 'admin');
$tb_user->addColumn('user_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_user->addColumn('username',  'varchar(32)');
$tb_user->addColumn('password',  'varchar(128)');
$tb_user->addColumn('user_type', 'varchar(32)');
$tb_user->addColumn('active',    'boolean');
$tb_user->addColumn('edited',    'datetime');
$tb_user->addColumn('created',   'datetime');
$tbs[] = $tb_user;

$tb_user_customer = new TableModel('toolbox', 'user_customer', 'admin');
$tb_user_customer->addColumn('user_customer_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_user_customer->addColumn('user_id',          'int');
$tb_user_customer->addColumn('customer_id',      'int');
$tbs[] = $tb_user_customer;

return $tbs;

