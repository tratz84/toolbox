<?php

use core\db\TableModel;

$tbs = array();


$tb_activity = new TableModel('base', 'activity');
$tb_activity->addColumn('activity_id',       'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_activity->addColumn('user_id',           'int');
$tb_activity->addColumn('username',          'varchar(128)');
$tb_activity->addColumn('company_id',        'int');
$tb_activity->addColumn('person_id',         'int');
$tb_activity->addColumn('ref_object',        'varchar(48)');
$tb_activity->addColumn('ref_id',            'int');
$tb_activity->addColumn('code',              'varchar(32)');
$tb_activity->addColumn('short_description', 'text');
$tb_activity->addColumn('long_description',  'text');
$tb_activity->addColumn('note',              'text');
$tb_activity->addColumn('changes',           'text');
$tb_activity->addColumn('created',           'datetime');
$tbs[] = $tb_activity;

$tb_cron = new TableModel('base', 'cron');
$tb_cron->addColumn('cron_id',     'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_cron->addColumn('cron_name',   'varchar(128)');
$tb_cron->addColumn('last_status', 'varchar(128)');
$tb_cron->addColumn('last_run',    'datetime');
$tb_cron->addColumn('running',     'boolean');
$tbs[] = $tb_cron;


$tb_cron_run = new TableModel('base', 'cron_run');
$tb_cron_run->addColumn('cron_run_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_cron_run->addColumn('cron_id',     'int');
$tb_cron_run->addColumn('message',     'text');
$tb_cron_run->addColumn('error',       'text');
$tb_cron_run->addColumn('status',      'varchar(64)');
$tb_cron_run->addColumn('created',     'datetime');
$tbs[] = $tb_cron_run;

$tb_file = new TableModel('base', 'file');
$tb_file->addColumn('file_id',       'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_file->addColumn('ref_count',     'int');
$tb_file->addColumn('filename',      'varchar(255)');
$tb_file->addColumn('filesize',      'int');
$tb_file->addColumn('module_name',   'varchar(128)');
$tb_file->addColumn('category_name', 'varchar(128)');
$tb_file->addColumn('edited',        'datetime');
$tb_file->addColumn('created',       'datetime');
$tbs[] = $tb_file;


$tb_menu = new TableModel('base', 'menu');
$tb_menu->addColumn('menu_id',          'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_menu->addColumn('menu_code',        'varchar(64)');
$tb_menu->addColumn('parent_menu_code', 'varchar(64)');
$tb_menu->addColumn('sort',             'int');
$tb_menu->addColumn('visible',          'boolean');
$tb_menu->addIndex('menu_code', array('menu_code'), ['unique' => true]);
$tbs[] = $tb_menu;

$tb_multiuser_lock = new TableModel('base', 'multiuser_lock');
$tb_multiuser_lock->addColumn('username', 'varchar(128)', ['key' => 'PRIMARY KEY']);
$tb_multiuser_lock->addColumn('tabuid',   'varchar(48)', ['key' => 'PRIMARY KEY']);
$tb_multiuser_lock->addColumn('lock_key', 'varchar(255)');
$tb_multiuser_lock->addColumn('ip',       'varchar(128)');
$tb_multiuser_lock->addColumn('created',  'datetime');
$tbs[] = $tb_multiuser_lock;


$tb_object_meta = new TableModel('base', 'object_meta');
$tb_object_meta->addColumn('object_meta_id', 'bigint', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_object_meta->addColumn('object_name',    'varchar(128)');
$tb_object_meta->addColumn('object_key',     'varchar(128)');
$tb_object_meta->addColumn('object_id',      'int');
$tb_object_meta->addColumn('object_value',   'longtext');
$tb_object_meta->addColumn('object_note',    'text');
$tb_object_meta->addIndex('index_key_id', array('object_name', 'object_key', 'object_id'), ['unique' => true]);
$tbs[] = $tb_object_meta;



$tb_reset_password = new TableModel('base', 'reset_password');
$tb_reset_password->addColumn('reset_password_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_reset_password->addColumn('user_id',           'int');
$tb_reset_password->addColumn('username',          'varchar(128)');
$tb_reset_password->addColumn('security_string',   'varchar(128)');
$tb_reset_password->addColumn('request_ip',        'varchar(64)');
$tb_reset_password->addColumn('used_ip',           'varchar(64)');
$tb_reset_password->addColumn('used',              'datetime');
$tb_reset_password->addColumn('created',           'datetime');
$tbs[] = $tb_reset_password;


$tb_setting = new TableModel('base', 'setting');
$tb_setting->addColumn('setting_id',        'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_setting->addColumn('setting_type',      'varchar(16)');
$tb_setting->addColumn('setting_code',      'varchar(64)');
$tb_setting->addColumn('short_description', 'longtext');
$tb_setting->addColumn('long_description',  'longtext');
$tb_setting->addColumn('text_value',        'longtext');
$tb_setting->addIndex('setting_code', array('setting_code'));
$tbs[] = $tb_setting;


$tb_user = new TableModel('base', 'user');
$tb_user->addColumn('user_id',         'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_user->addColumn('username',        'varchar(128)');
$tb_user->addColumn('email',           'varchar(255)');
$tb_user->addColumn('password',        'varchar(255)');
$tb_user->addColumn('edited',          'datetime');
$tb_user->addColumn('created',         'datetime');
$tb_user->addColumn('user_type',       'varchar(20)');
$tb_user->addColumn('firstname',       'varchar(128)');
$tb_user->addColumn('lastname',        'varchar(128)');
$tb_user->addColumn('autologin_token', 'varchar(255)');
$tb_user->addColumn('activated',       'boolean');
$tb_user->addIndex('username', array('username'), ['unique' => true]);
$tbs[] = $tb_user;


$tb_user_cap = new TableModel('base', 'user_capability');
$tb_user_cap->addColumn('user_capability_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_user_cap->addColumn('user_id',            'int');
$tb_user_cap->addColumn('module_name',        'varchar(32)');
$tb_user_cap->addColumn('capability_code',    'varchar(64)');
$tb_user_cap->addColumn('created',            'datetime');
$tbs[] = $tb_user_cap;


$tb_user_ip = new TableModel('base', 'user_ip');
$tb_user_ip->addColumn('user_ip_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_user_ip->addColumn('user_id',    'int');
$tb_user_ip->addColumn('ip',         'varchar(60)');
$tbs[] = $tb_user_ip;




$tb_note = new TableModel('base', 'note');
$tb_note->addColumn('note_id',           'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_note->addColumn('ref_object',        'varchar(32)');
$tb_note->addColumn('ref_id',            'int');
$tb_note->addColumn('company_id',        'int');
$tb_note->addColumn('person_id',         'int');
$tb_note->addColumn('short_note',        'text');
$tb_note->addColumn('long_note',         'text');
$tb_note->addColumn('important',         'boolean');
$tb_note->addColumn('edited',            'datetime');
$tb_note->addColumn('created',           'datetime');
$tbs[] = $tb_note;



return $tbs;
