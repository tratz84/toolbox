<?php


use core\db\TableModel;

$tbs = array();

$tb_message = new TableModel('signrequest', 'message');
$tb_message->addColumn('message_id',            'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_message->addColumn('ref_object',            'varchar(32)');
$tb_message->addColumn('ref_id',                'int');
$tb_message->addColumn('from_name',             'varchar(255)');
$tb_message->addColumn('from_email',            'varchar(255)');
$tb_message->addColumn('message',               'text');
$tb_message->addColumn('documents_response',    'text');
$tb_message->addColumn('signrequests_response', 'text');
$tb_message->addColumn('sent',                  'boolean');
$tb_message->addColumn('edited',                'datetime');
$tb_message->addColumn('created',               'datetime');
$tbs[] = $tb_message;

$tb_message_signer = new TableModel('signrequest', 'message_signer');
$tb_message_signer->addColumn('message_signer_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_message_signer->addColumn('message_id',        'int');
$tb_message_signer->addColumn('signer_email',      'varchar(255)');
$tb_message_signer->addColumn('signer_name',       'varchar(255)');
$tbs[] = $tb_message_signer;


return $tbs;

