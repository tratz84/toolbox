<?php


use core\db\TableModel;

$tbs = array();

$tb_pagequeue = new TableModel('filesync', 'pagequeue');
$tb_pagequeue->addColumn('pagequeue_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_pagequeue->addColumn('ref_id',           'int');
$tb_pagequeue->addColumn('ref_object',       'varchar(255)');
$tb_pagequeue->addColumn('user_id',          'int');
$tb_pagequeue->addColumn('filename',         'varchar(255)');
$tb_pagequeue->addColumn('name',             'varchar(255)');
$tb_pagequeue->addColumn('description',      'text');
$tb_pagequeue->addColumn('crop_x1',          'double');
$tb_pagequeue->addColumn('crop_y1',          'double');
$tb_pagequeue->addColumn('crop_x2',          'double');
$tb_pagequeue->addColumn('crop_y2',          'double');
$tb_pagequeue->addColumn('degrees_rotated',  'int');
$tb_pagequeue->addColumn('page_orientation', "enum('P','L')");
$tb_pagequeue->addColumn('edited',           'datetime');
$tb_pagequeue->addColumn('created',          'datetime');
$tbs[] = $tb_pagequeue;

$tb_filesync_store = new TableModel('filesync', 'store');
$tb_filesync_store->addColumn('store_id',         'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_filesync_store->addColumn('store_type',       'varchar(16)');
$tb_filesync_store->addColumn('store_name',       'varchar(128)');
$tb_filesync_store->addColumn('note',             'mediumtext');
$tb_filesync_store->addColumn('last_file_change', 'bigint');
$tb_filesync_store->addColumn('edited',           'datetime');
$tb_filesync_store->addColumn('created',          'datetime');
$tb_filesync_store->addIndex('store_name', ['store_name'], ['unique' => true]);
$tbs[] = $tb_filesync_store;


$tb_filesync_store_file = new TableModel('filesync', 'store_file');
$tb_filesync_store_file->addColumn('store_file_id', 'bigint', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_filesync_store_file->addColumn('store_id',      'int');
$tb_filesync_store_file->addColumn('path',          'varchar(255)');
$tb_filesync_store_file->addColumn('rev',           'int');
$tb_filesync_store_file->addColumn('deleted',       'boolean');
$tb_filesync_store_file->addColumn('edited',        'datetime');
$tb_filesync_store_file->addColumn('created',       'datetime');
$tb_filesync_store_file->addIndex('store_id', ['store_id']);
$tb_filesync_store_file->addForeignKey('filesync__store_file_ibfk_1', 'store_id', 'filesync__store', 'store_id', 'CASCADE', 'RESTRICT');
$tbs[] = $tb_filesync_store_file;

$tb_filesync_store_file_meta = new TableModel('filesync', 'store_file_meta');
$tb_filesync_store_file_meta->addColumn('store_file_meta_id', 'bigint', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_filesync_store_file_meta->addColumn('store_file_id',      'bigint');
$tb_filesync_store_file_meta->addColumn('company_id',         'int');
$tb_filesync_store_file_meta->addColumn('person_id',          'int');
$tb_filesync_store_file_meta->addColumn('subject',            'varchar(255)');
$tb_filesync_store_file_meta->addColumn('long_description',   'text');
$tb_filesync_store_file_meta->addColumn('document_date',      'date');
$tb_filesync_store_file_meta->addColumn('public',             'bool', ['default' => 0]);
$tb_filesync_store_file_meta->addColumn('public_secret',      'varchar(64)');
$tb_filesync_store_file_meta->addIndex('store_file_id', ['store_file_id'], ['unique' => true]);
$tb_filesync_store_file_meta->addIndex('company_id', ['company_id']);
$tb_filesync_store_file_meta->addForeignKey('filesync__store_file_meta_ibfk_1', 'company_id', 'customer__company', 'company_id', 'RESTRICT', 'RESTRICT');
$tb_filesync_store_file_meta->addForeignKey('filesync__store_file_meta_ibfk_2', 'store_file_id', 'filesync__store_file', 'store_file_id', 'RESTRICT', 'RESTRICT');
$tbs[] = $tb_filesync_store_file_meta;


$tb_filesync_store_file_meta_tag = new TableModel('filesync', 'store_file_meta_tag');
$tb_filesync_store_file_meta_tag->addColumn('meta_tag_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_filesync_store_file_meta_tag->addColumn('tag_name',    'varchar(255)');
$tb_filesync_store_file_meta_tag->addColumn('sort',        'int');
$tb_filesync_store_file_meta_tag->addColumn('visible',     'boolean');
$tb_filesync_store_file_meta_tag->addColumn('edited',      'datetime');
$tb_filesync_store_file_meta_tag->addColumn('created',     'datetime');
$tbs[] = $tb_filesync_store_file_meta_tag;

$tb_filesync_store_file_rev = new TableModel('filesync', 'store_file_rev');
$tb_filesync_store_file_rev->addColumn('store_file_rev_id', 'bigint', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_filesync_store_file_rev->addColumn('store_file_id',     'bigint');
$tb_filesync_store_file_rev->addColumn('filesize',          'bigint');
$tb_filesync_store_file_rev->addColumn('md5sum',            'varchar(32)');
$tb_filesync_store_file_rev->addColumn('rev',               'int');
$tb_filesync_store_file_rev->addColumn('lastmodified',      'datetime');
$tb_filesync_store_file_rev->addColumn('encrypted',         'boolean');
$tb_filesync_store_file_rev->addColumn('created',           'datetime');
$tb_filesync_store_file_rev->addIndex('uc_store_file_id_rev', ['store_file_id', 'rev'], ['unique' => true]);
$tb_filesync_store_file_rev->addForeignKey('filesync__store_file_rev_ibfk_1', 'store_file_id', 'filesync__store_file', 'store_file_id', 'CASCADE', 'RESTRICT');
$tbs[] = $tb_filesync_store_file_rev;




return $tbs;


