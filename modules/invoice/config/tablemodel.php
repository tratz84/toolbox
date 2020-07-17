<?php


use core\db\TableModel;

$tbs = array();


$tb_ag = new TableModel('article', 'article_group');
$tb_ag->addColumn('article_group_id',        'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_ag->addColumn('parent_article_group_id', 'int');
$tb_ag->addColumn('group_name',              'varchar(255)');
$tb_ag->addColumn('long_description1',       'mediumtext');
$tb_ag->addColumn('long_description2',       'mediumtext');
$tb_ag->addColumn('active',                  'boolean');
$tb_ag->addColumn('sort',                    'int');
$tb_ag->addColumn('edited',                  'datetime');
$tb_ag->addColumn('created',                 'datetime');
$tb_ag->addForeignKey('article__article_group_ibfk_1', 'parent_article_group_id', 'article__article_group', 'article_group_id', 'SET NULL', 'RESTRICT');
$tbs[] = $tb_ag;

$tb_vat = new TableModel('invoice', 'vat');
$tb_vat->addColumn('vat_id',           'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_vat->addColumn('description',      'varchar(255)');
$tb_vat->addColumn('percentage',       'double');
$tb_vat->addColumn('visible',          'boolean');
$tb_vat->addColumn('default_selected', 'boolean');
$tb_vat->addColumn('sort',             'int');
$tbs[] = $tb_vat;

$tb_art = new TableModel('article', 'article');
$tb_art->addColumn('article_id',              'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_art->addColumn('article_type',            'varchar(32)');
$tb_art->addColumn('article_name',            'varchar(255)');
$tb_art->addColumn('long_description1',       'mediumtext');
$tb_art->addColumn('long_description2',       'mediumtext');
$tb_art->addColumn('price',                   'double');
$tb_art->addColumn('rentable',                'boolean');
$tb_art->addColumn('simultaneously_rentable', 'int');
$tb_art->addColumn('price_type',              'varchar(16)');
$tb_art->addColumn('vat_price',               'bigint');
$tb_art->addColumn('vat_id',                  'int');
$tb_art->addColumn('active',                  'boolean');
$tb_art->addColumn('deleted',                 'boolean');
$tb_art->addColumn('edited',                  'datetime');
$tb_art->addColumn('created',                 'datetime');
$tb_art->addForeignKey('article__article_ibfk_1', 'vat_id', 'invoice__vat', 'vat_id', 'SET NULL', 'RESTRICT');
$tbs[] = $tb_art;

$tb_aag = new TableModel('article', 'article_article_group');
$tb_aag->addColumn('article_group_id', 'int', ['key' => 'PRIMARY KEY']);
$tb_aag->addColumn('article_id',       'int', ['key' => 'PRIMARY KEY']);
$tb_aag->addColumn('sort',             'int');
$tb_aag->addForeignKey('article__article_article_group_ibfk_1', 'article_group_id', 'article__article_group', 'article_group_id', 'CASCADE', 'RESTRICT');
$tb_aag->addForeignKey('article__article_article_group_ibfk_2', 'article_id', 'article__article', 'article_id', 'CASCADE', 'RESTRICT');
$tbs[] = $tb_aag;


$tb_cs = new TableModel('invoice', 'company_setting');
$tb_cs->addColumn('company_setting_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_cs->addColumn('company_id',         'int');
$tb_cs->addColumn('tax_shift',          'boolean');
$tb_cs->addColumn('tax_excemption',     'boolean');
$tb_cs->addColumn('payment_term',       'int');
$tb_cs->addIndex('company_id', ['company_id'], ['unique' => true]);
// $tb_cs->addForeignKey('invoice__company_setting_ibfk_1', 'company_id', 'customer__company', 'company_id', 'RESTRICT', 'RESTRICT');
$tbs[] = $tb_cs;

$tb_is = new TableModel('invoice', 'invoice_status');
$tb_is->addColumn('invoice_status_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_is->addColumn('description',       'varchar(255)');
$tb_is->addColumn('default_selected',  'boolean');
$tb_is->addColumn('active',            'boolean');
$tb_is->addColumn('sort',              'int');
$tbs[] = $tb_is;

$tb_invoice = new TableModel('invoice', 'invoice');
$tb_invoice->addColumn('invoice_id',                      'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_invoice->addColumn('ref_invoice_id',                  'int');
$tb_invoice->addColumn('company_id',                      'int');
$tb_invoice->addColumn('person_id',                       'int');
$tb_invoice->addColumn('invoice_status_id',               'int');
$tb_invoice->addColumn('credit_invoice',                  'boolean');
$tb_invoice->addColumn('tax_shift',                       'boolean');
$tb_invoice->addColumn('invoice_number',                  'int');
$tb_invoice->addColumn('subject',                         'varchar(255)');
$tb_invoice->addColumn('comment',                         'mediumtext');
$tb_invoice->addColumn('note',                            'text');
$tb_invoice->addColumn('total_calculated_price',          'decimal(10,2)');
$tb_invoice->addColumn('total_calculated_price_incl_vat', 'decimal(10,2)');
$tb_invoice->addColumn('invoice_date',                    'date');
$tb_invoice->addColumn('edited',                          'datetime');
$tb_invoice->addColumn('created',                         'datetime');
$tb_invoice->addIndex('invoice_number', ['invoice_number'], ['unique' => true]);
$tb_invoice->addForeignKey('invoice__invoice_ibfk_1', 'invoice_status_id', 'invoice__invoice_status', 'invoice_status_id', 'RESTRICT', 'RESTRICT');
$tbs[] = $tb_invoice;

$tb_il = new TableModel('invoice', 'invoice_line');
$tb_il->addColumn('invoice_line_id',   'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_il->addColumn('invoice_id',        'int');
$tb_il->addColumn('article_id',        'int');
$tb_il->addColumn('short_description', 'varchar(255)');
$tb_il->addColumn('amount',            'double');
$tb_il->addColumn('price',             'decimal(10,2)');
$tb_il->addColumn('vat_percentage',    'double');
$tb_il->addColumn('vat_amount',        'decimal(10,2)');
$tb_il->addColumn('sort',              'int');
$tb_il->addColumn('edited',            'datetime');
$tb_il->addColumn('created',           'datetime');
$tb_il->addForeignKey('invoice__invoice_line_ibfk_1', 'invoice_id', 'invoice__invoice', 'invoice_id', 'CASCADE', 'RESTRICT');
$tbs[] = $tb_il;

$tb_os = new TableModel('invoice', 'offer_status');
$tb_os->addColumn('offer_status_id',  'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_os->addColumn('description',      'varchar(255)');
$tb_os->addColumn('default_selected', 'boolean');
$tb_os->addColumn('active',           'boolean');
$tb_os->addColumn('sort',             'int');
$tbs[] = $tb_os;


$tb_offer = new TableModel('invoice', 'offer');
$tb_offer->addColumn('offer_id',                        'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_offer->addColumn('company_id',                      'int');
$tb_offer->addColumn('person_id',                       'int');
$tb_offer->addColumn('offer_number',                    'varchar(16)');
$tb_offer->addColumn('offer_status_id',                 'int');
$tb_offer->addColumn('subject',                         'varchar(255)');
$tb_offer->addColumn('comment',                         'mediumtext');
$tb_offer->addColumn('note',                            'text');
$tb_offer->addColumn('total_calculated_price',          'decimal(10,2)');
$tb_offer->addColumn('total_calculated_price_incl_vat', 'decimal(10,2)');
$tb_offer->addColumn('accepted',                        'boolean');
$tb_offer->addColumn('offer_date',                      'date');
$tb_offer->addColumn('deposit',                         'double');
$tb_offer->addColumn('payment_upfront',                 'double');
$tb_offer->addColumn('edited',                          'datetime');
$tb_offer->addColumn('created',                         'datetime');
$tb_offer->addIndex('offer_status_id', ['offer_status_id']);
$tb_offer->addForeignKey('invoice__offer_ibfk_1', 'offer_status_id', 'invoice__offer_status', 'offer_status_id', 'RESTRICT', 'RESTRICT');
$tbs[] = $tb_offer;


$tb_offer_file = new TableModel('invoice', 'offer_file');
$tb_offer_file->addColumn('offer_file_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_offer_file->addColumn('offer_id',      'int');
$tb_offer_file->addColumn('file_id',       'int');
$tb_offer_file->addColumn('sort',          'int');
$tb_offer_file->addIndex('uq_offer_id_file_id', ['offer_id', 'file_id'], ['unique' => true]);
$tb_offer_file->addIndex('file_id', ['file_id']);
$tb_offer_file->addForeignKey('invoice__offer_file_ibfk_1', 'file_id', 'base__file', 'file_id', 'CASCADE', 'RESTRICT');
$tbs[] = $tb_offer_file;


$tb_offer_line = new TableModel('invoice', 'offer_line');
$tb_offer_line->addColumn('offer_line_id',      'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_offer_line->addColumn('offer_id',           'int');
$tb_offer_line->addColumn('article_id',         'int');
$tb_offer_line->addColumn('short_description',  'varchar(255)');
$tb_offer_line->addColumn('short_description2', 'varchar(255)');
$tb_offer_line->addColumn('long_description',   'mediumtext');
$tb_offer_line->addColumn('amount',             'double');
$tb_offer_line->addColumn('price',              'double');
$tb_offer_line->addColumn('vat',                'double');
$tb_offer_line->addColumn('line_type',          'varchar(16)');
$tb_offer_line->addColumn('sort',               'int');
$tb_offer_line->addColumn('edited',             'datetime');
$tb_offer_line->addColumn('created',            'datetime');
$tb_offer_line->addIndex('offer_id', ['offer_id']);
$tb_offer_line->addForeignKey('invoice__offer_line_ibfk_1', 'offer_id', 'invoice__offer', 'offer_id', 'CASCADE', 'RESTRICT');
$tbs[] = $tb_offer_line;

$tb_pa = new TableModel('invoice', 'price_adjustment');
$tb_pa->addColumn('price_adjustment_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_pa->addColumn('company_id',          'int');
$tb_pa->addColumn('person_id',           'int');
$tb_pa->addColumn('ref_object',          'varchar(32)');
$tb_pa->addColumn('ref_id',              'int');
$tb_pa->addColumn('new_price',           'decimal(10,2)');
$tb_pa->addColumn('new_discount',        'decimal(10,2)');
$tb_pa->addColumn('start_date',          'date');
$tb_pa->addColumn('executed',            'boolean');
$tb_pa->addColumn('created',             'datetime');
$tb_pa->addIndex('ref_object', ['ref_object', 'ref_id']);
$tbs[] = $tb_pa;


$tb_to_bill = new TableModel('invoice', 'to_bill');
$tb_to_bill->renameColumn('billed', 'paid');
$tb_to_bill->addColumn('to_bill_id',        'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_to_bill->addColumn('company_id',        'int');
$tb_to_bill->addColumn('person_id',         'int');
$tb_to_bill->addColumn('project_id',        'int');
$tb_to_bill->addColumn('user_id',           'int');
$tb_to_bill->addColumn('type',              "enum('debet','credit')");
$tb_to_bill->addColumn('short_description', 'varchar(255)');
$tb_to_bill->addColumn('long_description',  'text');
$tb_to_bill->addColumn('amount',            'double');
$tb_to_bill->addColumn('price',             'decimal(10,2)');
$tb_to_bill->addColumn('invoice_line_id',   'int');
$tb_to_bill->addColumn('paid',              'boolean', ['default' => 0]);
$tb_to_bill->addColumn('deleted',           'datetime');
$tb_to_bill->addColumn('edited',            'datetime');
$tb_to_bill->addColumn('created',           'datetime');
$tb_to_bill->addForeignKey('invoice__to_bill_ibfk_1', 'company_id', 'customer__company', 'company_id', 'SET NULL', 'CASCADE');
// $tb_to_bill->addForeignKey('invoice__to_bill_ibfk_2', 'project_id', 'project__project', 'project_id', 'SET NULL', 'CASCADE');
$tb_to_bill->addForeignKey('invoice__to_bill_ibfk_3', 'user_id', 'base__user', 'user_id', 'SET NULL', 'CASCADE');
$tb_to_bill->addForeignKey('invoice__to_bill_ibfk_4', 'invoice_line_id', 'invoice__invoice_line', 'invoice_line_id', 'SET NULL', 'CASCADE');
$tbs[] = $tb_to_bill;


return $tbs;

