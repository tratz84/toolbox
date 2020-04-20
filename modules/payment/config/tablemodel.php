<?php

use core\db\TableModel;

$tbs = array();

$tb_payment = new TableModel('payment', 'payment');
$tb_payment->addColumn('payment_id',   'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_payment->addColumn('person_id',    'int');
$tb_payment->addColumn('company_id',   'int');
$tb_payment->addColumn('description',  'varchar(255)');
$tb_payment->addColumn('note',         'text');
$tb_payment->addColumn('amount',       'decimal(10,2)');
$tb_payment->addColumn('payment_date', 'date');
$tb_payment->addColumn('cancelled',    'boolean');
$tb_payment->addColumn('created',      'datetime');
$tbs[] = $tb_payment;

$tb_pl = new TableModel('payment', 'payment_line');
$tb_pl->addColumn('payment_line_id',      'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_pl->addColumn('payment_id',           'int');
$tb_pl->addColumn('payment_method_id',    'int');
$tb_pl->addColumn('amount',               'decimal(10,2)');
$tb_pl->addColumn('bankaccountno',        'varchar(40)');
$tb_pl->addColumn('bankaccountno_contra', 'varchar(40)');
$tb_pl->addColumn('code',                 'varchar(16)');
$tb_pl->addColumn('name',                 'varchar(255)');
$tb_pl->addColumn('description1',         'text');
$tb_pl->addColumn('description2',         'text');
$tb_pl->addColumn('mutation_type',        'varchar(64)');
$tb_pl->addColumn('sort',                 'int');
$tbs[] = $tb_pl;

$tb_payment_ref = new TableModel('payment', 'payment_ref');
$tb_payment_ref->addColumn('payment_ref_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_payment_ref->addColumn('payment_id',     'int');
$tb_payment_ref->addColumn('ref_object',     'varchar(32)');
$tb_payment_ref->addColumn('ref_id',         'int');
$tbs[] = $tb_payment_ref;

$tb_payment_method = new TableModel('payment', 'payment_method');
$tb_payment_method->addColumn('payment_method_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_payment_method->addColumn('code',              'varchar(16)');
$tb_payment_method->addColumn('description',       'varchar(255)');
$tb_payment_method->addColumn('note',              'text');
$tb_payment_method->addColumn('sort',              'int');
$tb_payment_method->addColumn('default_selected',  'boolean');
$tb_payment_method->addColumn('active',            'boolean');
$tb_payment_method->addColumn('deleted',           'boolean');
$tb_payment_method->addColumn('edited',            'datetime');
$tb_payment_method->addColumn('created',           'datetime');
$tb_payment_method->addIndex('uq_code', ['code'], ['unique' => true]);
$tbs[] = $tb_payment_method;

$tb_payment_import = new TableModel('payment', 'payment_import');
$tb_payment_import->addColumn('payment_import_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_payment_import->addColumn('description',       'varchar(255)');
$tb_payment_import->addColumn('status',            'varchar(16)');
$tb_payment_import->addColumn('created',           'datetime');
$tbs[] = $tb_payment_import;

$tb_pil = new TableModel('payment', 'payment_import_line');
$tb_pil->addColumn('payment_import_line_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_pil->addColumn('payment_import_id',      'int');
$tb_pil->addColumn('transaction_id',         'int');
$tb_pil->addColumn('debet_credit',           'varchar(1)');
$tb_pil->addColumn('amount',                 'decimal(10,2)');
$tb_pil->addColumn('bankaccountno',          'varchar(64)');
$tb_pil->addColumn('bankaccountno_contra',   'varchar(64)');
$tb_pil->addColumn('payment_date',           'date');
$tb_pil->addColumn('name',                   'varchar(255)');
$tb_pil->addColumn('description',            'varchar(512)');
$tb_pil->addColumn('code',                   'varchar(16)');
$tb_pil->addColumn('mutation_type',          'varchar(32)');
$tb_pil->addColumn('company_id',             'int');
$tb_pil->addColumn('person_id',              'int');
$tb_pil->addColumn('invoice_id',             'int');
$tb_pil->addColumn('import_status',          'varchar(16)');
$tbs[] = $tb_pil;

return $tbs;

