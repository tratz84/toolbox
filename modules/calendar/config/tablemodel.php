<?php

use core\db\TableModel;

$tbs = array();


$tb_cal = new TableModel('cal', 'calendar');
$tb_cal->addColumn('calendar_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_cal->addColumn('name',        'varchar(255)');
$tb_cal->addColumn('user_id',     'int');
$tb_cal->addColumn('secret_key',  'varchar(64)');
$tb_cal->addColumn('active',      'boolean');
$tb_cal->addColumn('deleted',     'datetime');
$tb_cal->addColumn('edited',      'datetime');
$tb_cal->addColumn('created',     'datetime');
$tb_cal->addIndex('cal__calendar_ibfk_1', array('user_id'));
$tbs[] = $tb_cal;


$tb_ci = new TableModel('cal', 'calendar_item');
$tb_ci->addColumn('calendar_item_id',          'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_ci->addColumn('ref_calendar_item_id',      'int');
$tb_ci->addColumn('calendar_id',               'int');
$tb_ci->addColumn('calendar_item_status_id',   'int');
$tb_ci->addColumn('calendar_item_category_id', 'int');
$tb_ci->addColumn('title',                     'varchar(255)');
$tb_ci->addColumn('location',                  'varchar(255)');
$tb_ci->addColumn('all_day',                   'boolean');
$tb_ci->addColumn('private',                   'boolean');
$tb_ci->addColumn('start_date',                'date');
$tb_ci->addColumn('start_time',                'time');
$tb_ci->addColumn('end_date',                  'date');
$tb_ci->addColumn('end_time',                  'time');
$tb_ci->addColumn('reminder',                  'int');
$tb_ci->addColumn('recurrence_type',           'varchar(16)');
$tb_ci->addColumn('recurrence_rule',           'varchar(255)');
$tb_ci->addColumn('message',                   'text');
$tb_ci->addColumn('exdate',                    'longtext');
$tb_ci->addColumn('cancelled',                 'boolean', ['default' => 0]);
$tb_ci->addColumn('deleted',                   'datetime');
$tb_ci->addColumn('edited',                    'datetime');
$tb_ci->addColumn('created',                   'datetime');
$tb_ci->addIndex('cal__calendar_item_ibfk_1', array('calendar_item_status_id'));
$tb_ci->addIndex('cal__calendar_item_ibfk_2', array('calendar_item_category_id'));
$tbs[] = $tb_ci;


$tb_cic = new TableModel('cal', 'calendar_item_category');
$tb_cic->addColumn('calendar_item_category_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_cic->addColumn('category_name',             'varchar(255)');
$tb_cic->addColumn('visible',                   'boolean');
$tb_cic->addColumn('deleted',                   'datetime');
$tb_cic->addColumn('edited',                    'datetime');
$tb_cic->addColumn('created',                   'datetime');
$tbs[] = $tb_cic;



$tb_cis = new TableModel('cal', 'calendar_item_status');
$tb_cis->addColumn('calendar_item_status_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_cis->addColumn('status_name',             'varchar(255)');
$tb_cis->addColumn('visible',                 'boolean');
$tb_cis->addColumn('deleted',                 'datetime');
$tb_cis->addColumn('edited',                  'datetime');
$tb_cis->addColumn('created',                 'datetime');
$tbs[] = $tb_cis;




$tb_todo = new TableModel('cal', 'todo');
$tb_todo->addColumn('todo_id',   'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_todo->addColumn('user_id',   'int');
$tb_todo->addColumn('list_name', 'varchar(255)');
$tb_todo->addColumn('edited',    'datetime');
$tb_todo->addColumn('created',   'datetime');
$tb_todo->addIndex('user_id', array('user_id'));
// TODO: FK, CONSTRAINT `cal__todo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL
$tbs[] = $tb_todo;

$tb_todo_item = new TableModel('cal', 'todo_item');
$tb_todo_item->addColumn('todo_item_id',     'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_todo_item->addColumn('todo_id',          'int');
$tb_todo_item->addColumn('summary',          'varchar(512)');
$tb_todo_item->addColumn('long_description', 'longtext');
$tb_todo_item->addColumn('start_date',       'datetime');
$tb_todo_item->addColumn('end_date',         'datetime');
$tb_todo_item->addColumn('priority',         'int');
$tb_todo_item->addColumn('status',           'int');
$tb_todo_item->addColumn('edited',           'datetime');
$tb_todo_item->addColumn('created',          'datetime');
$tb_todo_item->addIndex('todo_id', array('todo_id'));
// TODO: FK, CONSTRAINT `cal__todo_item_ibfk_1` FOREIGN KEY (`todo_id`) REFERENCES `cal__todo` (`todo_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
$tbs[] = $tb_todo_item;



return $tbs;


