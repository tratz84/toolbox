<?php

use core\db\TableModel;

// TODO: unique key constraints

$tbs = array();

$tb_mlog = new TableModel('mailing', 'log');
$tb_mlog->addColumn('log_id',      'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_mlog->addColumn('template_id', 'int');
$tb_mlog->addColumn('from_name',   'varchar(255)');
$tb_mlog->addColumn('from_email',  'varchar(255)');
$tb_mlog->addColumn('log_to',      'text');
$tb_mlog->addColumn('log_cc',      'text');
$tb_mlog->addColumn('log_bcc',     'text');
$tb_mlog->addColumn('subject',     'varchar(512)');
$tb_mlog->addColumn('content',     'text');
$tb_mlog->addColumn('created',     'datetime');
$tbs[] = $tb_mlog;


$tb_mtemplate = new TableModel('mailing', 'template');
$tb_mtemplate->addColumn('template_id',   'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_mtemplate->addColumn('template_code', 'varchar(64)');
$tb_mtemplate->addColumn('name',          'varchar(255)');
$tb_mtemplate->addColumn('from_name',     'varchar(255)');
$tb_mtemplate->addColumn('from_email',    'varchar(255)');
$tb_mtemplate->addColumn('subject',       'varchar(512)');
$tb_mtemplate->addColumn('content',       'text');
$tb_mtemplate->addColumn('active',        'boolean');
$tb_mtemplate->addColumn('sort',          'int');
$tb_mtemplate->addColumn('edited',        'datetime');
$tb_mtemplate->addColumn('created',       'datetime');
$tb_mtemplate->addIndex('template_code', array('template_code'), ['unique' => true]);
$tbs[] = $tb_mtemplate;


$tb_mtemplateto = new TableModel('mailing', 'template_to');
$tb_mtemplateto->addColumn('template_to_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_mtemplateto->addColumn('template_id',    'int');
$tb_mtemplateto->addColumn('to_type',        "enum('To','Cc','Bcc')");
$tb_mtemplateto->addColumn('to_name',        'varchar(255)');
$tb_mtemplateto->addColumn('to_email',       'varchar(255)');
$tb_mtemplateto->addColumn('sort',           'int');
$tbs[] =$tb_mtemplateto;



$tb_connector = new TableModel('webmail', 'connector');
$tb_connector->addColumn('connector_id',                  'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_connector->addColumn('user_id',                       'int');
$tb_connector->addColumn('description',                   'varchar(255)');
$tb_connector->addColumn('connector_type',                'varchar(16)');
$tb_connector->addColumn('hostname',                      'varchar(255)');
$tb_connector->addColumn('port',                          'int');
$tb_connector->addColumn('username',                      'varchar(255)');
$tb_connector->addColumn('password',                      'varchar(255)');
$tb_connector->addColumn('nextrun_fullimport',            'boolean');
$tb_connector->addColumn('sent_connector_imapfolder_id',  'int');
$tb_connector->addColumn('junk_connector_imapfolder_id',  'int');
$tb_connector->addColumn('trash_connector_imapfolder_id', 'int');
$tb_connector->addColumn('active',                        'boolean');
$tb_connector->addColumn('edited',                        'datetime');
$tb_connector->addColumn('created',                       'datetime');
$tb_connector->addIndex('email__connector_ibfk_1', array('user_id'));
// TODO:   CONSTRAINT `email__connector_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE RESTRICT
$tbs[] = $tb_connector;


$tb_cif = new TableModel('webmail', 'connector_imapfolder');
$tb_cif->addColumn('connector_imapfolder_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_cif->addColumn('connector_id',            'int');
$tb_cif->addColumn('folderName',              'varchar(255)');
$tb_cif->addColumn('attributes',              'int');
$tb_cif->addColumn('outgoing',                'boolean');
$tb_cif->addColumn('junk',                    'boolean');
$tb_cif->addColumn('active',                  'boolean');
$tb_cif->addColumn('edited',                  'datetime');
$tb_cif->addColumn('created',                 'datetime');
$tb_cif->addIndex('webmail__connector_imapfolder_ibfk_1', array('connector_id'));
// TODO:   CONSTRAINT `webmail__connector_imapfolder_ibfk_1` FOREIGN KEY (`connector_id`) REFERENCES `webmail__connector` (`connector_id`) ON DELETE CASCADE ON UPDATE RESTRICT
$tbs[] = $tb_cif;

$tb_email = new TableModel('webmail', 'email');
$tb_email->addColumn('email_id',                'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_email->addColumn('user_id',                 'int');
$tb_email->addColumn('company_id',              'int');
$tb_email->addColumn('person_id',               'int');
$tb_email->addColumn('identity_id',             'int');
$tb_email->addColumn('connector_id',            'int');
$tb_email->addColumn('connector_imapfolder_id', 'int');
$tb_email->addColumn('attributes',              'int');
$tb_email->addColumn('message_id',              'varchar(255)');
$tb_email->addColumn('spam',                    'boolean');
$tb_email->addColumn('incoming',                'boolean');
$tb_email->addColumn('from_name',               'varchar(255)');
$tb_email->addColumn('from_email',              'varchar(255)');
$tb_email->addColumn('subject',                 'varchar(255)');
$tb_email->addColumn('text_content',            'mediumtext');
$tb_email->addColumn('received',                'datetime');
$tb_email->addColumn('deleted',                 'datetime');
$tb_email->addColumn('status',                  'varchar(16)');
$tb_email->addColumn('created',                 'datetime');
$tb_email->addColumn('search_id',               'bigint');
$tb_email->addIndex('webmail__email_ibfk_1', array('user_id'));
$tb_email->addIndex('id_search_id', array('search_id'));
$tb_email->addIndex('connector_imapfolder_id', array('connector_imapfolder_id'));
$tb_email->addIndex('text_content', array('text_content'), ['fulltext' => true]);
// TODO: CONSTRAINT `webmail__email_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE RESTRICT
$tbs[] = $tb_email;


$tb_eet = new TableModel('webmail', 'email_email_tag');
$tb_eet->addColumn('email_email_tag_id',                'bigint', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_eet->addColumn('email_id', 'int');
$tb_eet->addColumn('email_tag_id', 'int');
$tb_eet->addIndex('uc_email_id_tag_id', array('email_id', 'email_tag_id'), ['unique' => true]);
$tb_eet->addIndex('webmail__email_email_tag_ibfk_2', array('email_tag_id'));
// TODO: CONSTRAINT `webmail__email_email_tag_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `webmail__email` (`email_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
// CONSTRTODO: AINT `webmail__email_email_tag_ibfk_2` FOREIGN KEY (`email_tag_id`) REFERENCES `webmail__email_tag` (`email_tag_id`) ON DELETE CASCADE ON UPDATE RESTRICT
$tbs[] = $tb_eet;



$tb_ef = new TableModel('webmail', 'email_file');
$tb_ef->addColumn('email_file_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_ef->addColumn('email_id',      'int');
$tb_ef->addColumn('filename',      'varchar(255)');
$tb_ef->addColumn('path',          'varchar(255)');
$tbs[] = $tb_ef;

$tb_es = new TableModel('webmail', 'email_status');
$tb_es->addColumn('email_status_id',  'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_es->addColumn('status_name',      'varchar(255)');
$tb_es->addColumn('default_selected', 'boolean');
$tb_es->addColumn('sort',             'int');
$tb_es->addColumn('visible',          'boolean');
$tb_es->addColumn('edited',           'datetime');
$tb_es->addColumn('created',          'datetime');
$tbs[] = $tb_es;


$tb_et = new TableModel('webmail', 'email_tag');
$tb_et->addColumn('email_tag_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_et->addColumn('tag_name',     'varchar(255)');
$tb_et->addColumn('sort',         'int');
$tb_et->addColumn('visible',      'boolean');
$tb_et->addColumn('edited',       'datetime');
$tb_et->addColumn('created',      'datetime');
$tbs[] = $tb_et;


$tb_eto = new TableModel('webmail', 'email_to');
$tb_eto->addColumn('email_to_id', 'bigint', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_eto->addColumn('email_id',    'int');
$tb_eto->addColumn('to_type',     "enum('To','Cc','Bcc')");
$tb_eto->addColumn('to_name',     'varchar(255)');
$tb_eto->addColumn('to_email',    'varchar(255)');
$tb_eto->addIndex('webmail__email_to_ibfk_1', array('email_id'));
$tbs[] = $tb_eto;

$tb_filter = new TableModel('webmail', 'filter');
$tb_filter->addColumn('filter_id',    'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_filter->addColumn('connector_id', 'int');
$tb_filter->addColumn('filter_name',  'varchar(255)');
$tb_filter->addColumn('match_method', "enum('match_all','match_one')");
$tb_filter->addColumn('sort',         'int');
$tb_filter->addColumn('active',       'boolean');
$tb_filter->addColumn('edited',       'datetime');
$tb_filter->addColumn('created',      'datetime');
$tbs[] = $tb_filter;

$tb_filter_action = new TableModel('webmail', 'filter_action');
$tb_filter_action->addColumn('filter_action_id',    'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_filter_action->addColumn('filter_id', 'int');
$tb_filter_action->addColumn('filter_action', 'varchar(255)');
$tb_filter_action->addColumn('filter_action_property', 'varchar(255)');
$tb_filter_action->addColumn('filter_action_value', 'varchar(255)');
$tb_filter_action->addColumn('sort', 'int');
$tb_filter_action->addIndex('webmail__filter_action_ibfk_1', array('filter_id'));
// TODO: CONSTRAINT `webmail__filter_action_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `webmail__filter` (`filter_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
$tbs[] = $tb_filter_action;

$tb_filter_condition = new TableModel('webmail', 'filter_condition');
$tb_filter_condition->addColumn('filter_condition_id',    'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_filter_condition->addColumn('filter_id', 'int');
$tb_filter_condition->addColumn('filter_field', 'varchar(255)');
$tb_filter_condition->addColumn('filter_type', 'varchar(255)');
$tb_filter_condition->addColumn('filter_pattern', 'varchar(255)');
$tb_filter_condition->addColumn('sort', 'int');
$tb_filter_condition->addIndex('webmail__filter_condition_ibfk_1', array('filter_id'));
// TODO: CONSTRAINT `webmail__filter_condition_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `webmail__filter` (`filter_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
$tbs[] = $tb_filter_condition;

$tb_identity = new TableModel('webmail', 'identity');
$tb_identity->addColumn('identity_id',  'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_identity->addColumn('connector_id', 'int');
$tb_identity->addColumn('from_name',    'varchar(255)');
$tb_identity->addColumn('from_email',   'varchar(255)');
$tb_identity->addColumn('active',       'boolean');
$tb_identity->addColumn('sort',         'int');
$tb_identity->addColumn('edited',       'datetime');
$tb_identity->addColumn('created',      'datetime');
$tb_identity->addIndex('webmail__identity_ibfk_1', array('connector_id'));
// TODO: CONSTRAINT `webmail__identity_ibfk_1` FOREIGN KEY (`connector_id`) REFERENCES `webmail__connector` (`connector_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
$tbs[] = $tb_identity;


// return $tbs;
return null;

