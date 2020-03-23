<?php

use core\db\TableModel;

$tbs = array();


$tb_activity = new TableModel('base', 'activity');
$tb_activity->addColumn('activity_id',       ' int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_activity->addColumn('user_id',           'int');
$tb_activity->addColumn('username',          'varchar(128)');
$tb_activity->addColumn('company_id',        'int');
$tb_activity->addColumn('person_id',         'int');
$tb_activity->addColumn('ref_object',        'varchar(32)');
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


$tb_country = new TableModel('customer', 'country');
$tb_country->addColumn('country_id',   'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_country->addColumn('name',         'varchar(255)');
$tb_country->addColumn('country_iso2', 'varchar(2)');
$tb_country->addColumn('country_iso3', 'varchar(3)');
$tb_country->addColumn('country_no',   'varchar(3)');
$tb_country->addColumn('phone_prefix', 'varchar(20)');
$tbs[] = $tb_country;


$tb_address = new TableModel('customer', 'address');
$tb_address->addColumn('address_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_address->addColumn('street',     'varchar(255)');
$tb_address->addColumn('street_no',  'varchar(64)');
$tb_address->addColumn('zipcode',    'varchar(64)');
$tb_address->addColumn('city',       'varchar(64)');
$tb_address->addColumn('note',       'longtext');
$tb_address->addColumn('sort',       'int');
$tb_address->addColumn('country_id', 'int');
$tb_address->addColumn('edited',     'datetime');
$tb_address->addColumn('created',    'datetime');
$tb_address->addIndex('country_id', array('country_id'));
// TODO: FK,  CONSTRAINT `customer__address_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `customer__country` (`country_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
$tbs[] = $tb_address;


$tb_company_type = new TableModel('customer', 'company_type');
$tb_company_type->addColumn('company_type_id',  'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_company_type->addColumn('type_name',        'varchar(64)');
$tb_company_type->addColumn('default_selected', 'boolean');
$tb_company_type->addColumn('sort',             'int');
$tbs[] = $tb_company_type;


$tb_company = new TableModel('customer', 'company');
$tb_company->addColumn('company_id',      'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_company->addColumn('company_name',    'varchar(255)');
$tb_company->addColumn('contact_person',  'varchar(128)');
$tb_company->addColumn('coc_number',      'varchar(128)');
$tb_company->addColumn('vat_number',      'varchar(64)');
$tb_company->addColumn('iban',            'varchar(64)');
$tb_company->addColumn('bic',             'varchar(32)');
$tb_company->addColumn('note',            'longtext');
$tb_company->addColumn('deleted',         'boolean');
$tb_company->addColumn('edited',          'datetime');
$tb_company->addColumn('created',         'datetime');
$tb_company->addColumn('company_type_id', 'int');
$tb_company->addIndex('company_type_id', array('company_type_id'));
// FK: TODO, CONSTRAINT `customer__company_ibfk_1` FOREIGN KEY (`company_type_id`) REFERENCES `customer__company_type` (`company_type_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
$tbs[] = $tb_company;

$tb_ca = new TableModel('customer', 'company_address');
$tb_ca->addColumn('company_address_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_ca->addColumn('company_id',         'int');
$tb_ca->addColumn('address_id',         'int');
$tb_ca->addColumn('sort',               'int');
$tb_ca->addIndex('company_id', array('company_id'));
$tb_ca->addIndex('address_id', array('address_id'));
// TODO: FK, CONSTRAINT `customer__company_address_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
// TODO: FK, CONSTRAINT `customer__company_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer__address` (`address_id`) ON DELETE CASCADE ON UPDATE RESTRICT
$tbs[] = $tb_ca;

$tb_email = new TableModel('customer', 'email');
$tb_email->addColumn('email_id',        'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_email->addColumn('email_address',   'varchar(255)');
$tb_email->addColumn('note',            'varchar(255)');
$tb_email->addColumn('description',     'longtext');
$tb_email->addColumn('primary_address', 'boolean');
$tb_email->addColumn('edited',          'datetime');
$tb_email->addColumn('created',         'datetime');
$tb_email->addColumn('sort',            'int');
$tbs[] = $tb_email;


$tb_ce = new TableModel('customer', 'company_email');
$tb_ce->addColumn('company_email_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_ce->addColumn('email_id',         'int');
$tb_ce->addColumn('company_id',       'int');
$tb_ce->addColumn('sort',             'int');
$tb_ce->addIndex('company_id', array('company_id'));
$tb_ce->addIndex('email_id', array('email_id'));
// TODO: FK, CONSTRAINT `customer__company_email_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
// TODO: FK, CONSTRAINT `customer__company_email_ibfk_3` FOREIGN KEY (`email_id`) REFERENCES `customer__email` (`email_id`) ON DELETE CASCADE ON UPDATE RESTRICT
$tbs[] = $tb_ce;


$tb_phone = new TableModel('customer', 'phone');
$tb_phone->addColumn('phone_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_phone->addColumn('phonenr',  'varchar(128)');
$tb_phone->addColumn('edited',   'datetime');
$tb_phone->addColumn('created',  'datetime');
$tb_phone->addColumn('note',     'longtext');
$tb_phone->addColumn('sort',     'int');
$tbs[] = $tb_phone;


$tb_company_phone = new TableModel('customer', 'company_phone');
$tb_company_phone->addColumn('company_phone_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_company_phone->addColumn('phone_id',         'int');
$tb_company_phone->addColumn('company_id',       'int');
$tb_company_phone->addColumn('sort',             'int');
$tb_company_phone->addIndex('company_id', array('company_id'));
$tb_company_phone->addIndex('phone_id', array('phone_id'));
// TODO: FK, CONSTRAINT `customer__company_phone_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
// TODO: FK, CONSTRAINT `customer__company_phone_ibfk_3` FOREIGN KEY (`phone_id`) REFERENCES `customer__phone` (`phone_id`) ON DELETE CASCADE ON UPDATE RESTRICT
$tbs[] = $tb_company_phone;


$tb_person = new TableModel('customer', 'person');
$tb_person->addColumn('person_id',       'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_person->addColumn('firstname',       'varchar(128)');
$tb_person->addColumn('insert_lastname', 'varchar(32)');
$tb_person->addColumn('lastname',        'varchar(128)');
$tb_person->addColumn('iban',            'varchar(64)');
$tb_person->addColumn('bic',             'varchar(32)');
$tb_person->addColumn('note',            'longtext');
$tb_person->addColumn('deleted',         'boolean');
$tb_person->addColumn('edited',          'datetime');
$tb_person->addColumn('created',         'datetime');
$tbs[] = $tb_person;


$tb_person_address = new TableModel('customer', 'person_address');
$tb_person_address->addColumn('person_address_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_person_address->addColumn('person_id',         'int');
$tb_person_address->addColumn('address_id',        'int');
$tb_person_address->addColumn('sort',              'int');
$tb_person_address->addIndex('person_id', array('person_id'));
$tb_person_address->addIndex('address_id', array('address_id'));
// TODO: FK, CONSTRAINT `customer__person_address_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
// TODO: FK, CONSTRAINT `customer__person_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer__address` (`address_id`) ON DELETE CASCADE ON UPDATE RESTRICT
$tbs[] = $tb_person_address;


$tb_person_email = new TableModel('customer', 'person_email');
$tb_person_email->addColumn('person_email_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_person_email->addColumn('email_id',        'int');
$tb_person_email->addColumn('person_id',       'int');
$tb_person_email->addColumn('sort',            'int');
$tb_person_email->addIndex('person_id', array('person_id'));
$tb_person_email->addIndex('email_id', array('email_id'));
// TODO: FK, CONSTRAINT `customer__person_email_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
// TODO: FK, CONSTRAINT `customer__person_email_ibfk_3` FOREIGN KEY (`email_id`) REFERENCES `customer__email` (`email_id`) ON DELETE CASCADE ON UPDATE RESTRICT
$tbs[] = $tb_person_email;


$tb_person_phone = new TableModel('customer', 'person_phone');
$tb_person_phone->addColumn('person_phone_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_person_phone->addColumn('person_id',       'int');
$tb_person_phone->addColumn('phone_id',        'int');
$tb_person_phone->addColumn('sort',            'int');
$tb_person_phone->addIndex('person_id', array('person_id'));
// TODO: FK, CONSTRAINT `customer__person_phone_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
// TODO: FK, CONSTRAINT `customer__person_phone_ibfk_2` FOREIGN KEY (`phone_id`) REFERENCES `customer__phone` (`phone_id`) ON DELETE CASCADE ON UPDATE RESTRICT
$tb_person_phone->addIndex('phone_id', array('phone_id'));
$tbs[] = $tb_person_phone;


return $tbs;
