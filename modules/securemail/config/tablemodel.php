<?php


use core\db\TableModel;

$tbs = array();


$tb_message = new TableModel('securemail', 'secure_message');
$tb_message->addColumn('secure_message_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);

$tb_message->addColumn('person_id',   'int');
$tb_message->addColumn('company_id',   'int');

$tb_message->addColumn('encryption_algo',   'varchar(16)');
$tb_message->addColumn('encryption_iv',     'text');
$tb_message->addColumn('password_hash',     'varchar(128)');
$tb_message->addColumn('password',          'varchar(128)');

$tb_message->addColumn('ttl_type',          'varchar(16)');     // once, or expires
$tb_message->addColumn('ttl_expires_on',    'datetime');

$tb_message->addColumn('short_description', 'text');
$tb_message->addColumn('encrypted_message', 'longtext');
$tb_message->addColumn('deleted',           'datetime');
$tb_message->addColumn('edited',            'datetime');
$tb_message->addColumn('created',           'datetime');
$tbs[] = $tb_message;


$tb_smlog = new TableModel('securemail', 'secure_message_log');
$tb_smlog->addColumn('secure_message_log_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_smlog->addColumn('secure_message_id', 'int');
$tb_smlog->addColumn('opened',            'boolean');
$tb_smlog->addColumn('log_message',       'varchar(64)');
$tb_smlog->addColumn('ip',                'varchar(46)');
$tb_smlog->addColumn('created',           'datetime');
$tbs[] = $tb_smlog;





return $tbs;

