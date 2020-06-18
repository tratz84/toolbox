<?php


use core\db\TableModel;

$tbs = array();

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
$tb_address->addForeignKey('customer__address_ibfk_1', 'country_id', 'customer__country', 'country_id', 'restrict', 'restrict');
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
$tb_company->addForeignKey('customer__company_ibfk_1', 'company_type_id', 'customer__company_type', 'company_type_id', 'restrict', 'restrict');
$tbs[] = $tb_company;

$tb_ca = new TableModel('customer', 'company_address');
$tb_ca->addColumn('company_address_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_ca->addColumn('company_id',         'int');
$tb_ca->addColumn('address_id',         'int');
$tb_ca->addColumn('sort',               'int');
$tb_ca->addIndex('company_id', array('company_id'));
$tb_ca->addIndex('address_id', array('address_id'));
$tb_ca->addForeignKey('customer__company_address_ibfk_1', 'company_id', 'customer__company', 'company_id', 'restrict', 'restrict');
$tb_ca->addForeignKey('customer__company_address_ibfk_2', 'address_id', 'customer__address', 'address_id', 'cascade', 'restrict');
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
$tb_ce->addForeignKey('customer__company_email_ibfk_2', 'company_id', 'customer__company', 'company_id', 'restrict', 'restrict');
$tb_ce->addForeignKey('customer__company_email_ibfk_3', 'email_id', 'customer__email', 'email_id', 'cascade', 'restrict');
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
$tb_company_phone->addForeignKey('customer__company_phone_ibfk_2', 'company_id', 'customer__company', 'company_id', 'restrict', 'restrict');
$tb_company_phone->addForeignKey('customer__company_phone_ibfk_3', 'phone_id', 'customer__phone', 'phone_id', 'cascade', 'restrict');
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
$tb_person_address->addForeignKey('customer__person_address_ibfk_1', 'person_id', 'customer__person', 'person_id', 'restrict', 'restrict');
$tb_person_address->addForeignKey('customer__person_address_ibfk_2', 'address_id', 'customer__address', 'address_id', 'cascade', 'restrict');
$tbs[] = $tb_person_address;


$tb_person_email = new TableModel('customer', 'person_email');
$tb_person_email->addColumn('person_email_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_person_email->addColumn('email_id',        'int');
$tb_person_email->addColumn('person_id',       'int');
$tb_person_email->addColumn('sort',            'int');
$tb_person_email->addIndex('person_id', array('person_id'));
$tb_person_email->addIndex('email_id', array('email_id'));
$tb_person_email->addForeignKey('customer__person_email_ibfk_2', 'person_id', 'customer__person', 'person_id', 'restrict', 'restrict');
$tb_person_email->addForeignKey('customer__person_email_ibfk_3', 'email_id', 'customer__email', 'email_id', 'cascade', 'restrict');
$tbs[] = $tb_person_email;


$tb_person_phone = new TableModel('customer', 'person_phone');
$tb_person_phone->addColumn('person_phone_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_person_phone->addColumn('person_id',       'int');
$tb_person_phone->addColumn('phone_id',        'int');
$tb_person_phone->addColumn('sort',            'int');
$tb_person_phone->addIndex('person_id', array('person_id'));
$tb_person_phone->addIndex('phone_id', array('phone_id'));
$tb_person_phone->addForeignKey('customer__person_phone_ibfk_1', 'person_id', 'customer__person', 'person_id', 'restrict', 'restrict');
$tb_person_phone->addForeignKey('customer__person_phone_ibfk_2', 'phone_id', 'customer__phone', 'phone_id', 'cascade', 'restrict');
$tbs[] = $tb_person_phone;


$tb_company_person = new TableModel('customer', 'company_person');
$tb_company_person->addColumn('company_person_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_company_person->addColumn('company_id',       'int');
$tb_company_person->addColumn('person_id',        'int');
$tb_company_person->addColumn('sort',             'int');
$tb_company_person->addForeignKey('fk_person',  'person_id',  'customer__person',  'person_id',  'cascade', 'cascade');
$tb_company_person->addForeignKey('fk_company', 'company_id', 'customer__company', 'company_id', 'cascade', 'cascade');
$tbs[] = $tb_company_person;


return $tbs;

