<?php


use core\db\TableModel;

$tbs = array();


$tb_project = new TableModel('project', 'project');
$tb_project->addColumn('project_id',             'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_project->addColumn('project_hours',          'int');
$tb_project->addColumn('project_billable_type',  'enum(\'fixed\',\'ongoing\')');
$tb_project->addColumn('hourly_rate',            'decimal(10,2)');
$tb_project->addColumn('company_id',             'int');
$tb_project->addColumn('person_id',              'int');
$tb_project->addColumn('project_name',           'varchar(255)');
$tb_project->addColumn('active',                 'boolean');
$tb_project->addColumn('note',                   'longtext');
$tb_project->addColumn('edited',                 'datetime');
$tb_project->addColumn('created',                'datetime');
$tb_project->addIndex('person_id', array('person_id'));
$tb_project->addIndex('company_id', array('company_id'));
$tbs[] = $tb_project;


$tb_phs = new TableModel('project', 'project_hour_status');
$tb_phs->addColumn('project_hour_status_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_phs->addColumn('description',            'varchar(64)');
$tb_phs->addColumn('default_selected',       'boolean');
$tb_phs->addColumn('sort',                   'int');
$tb_phs->addColumn('edited',                 'datetime');
$tb_phs->addColumn('created',                'datetime');
$tbs[] = $tb_phs;

$tb_pht = new TableModel('project', 'project_hour_type');
$tb_pht->addColumn('project_hour_type_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_pht->addColumn('description',            'longtext');
$tb_pht->addColumn('default_selected',       'boolean');
$tb_pht->addColumn('visible',                'boolean');
$tb_pht->addColumn('sort',                   'int');
$tb_pht->addColumn('edited',                 'datetime');
$tb_pht->addColumn('created',                'datetime');
$tbs[] = $tb_pht;


$tb_ph = new TableModel('project', 'project_hour');
$tb_ph->addColumn('project_hour_id',        'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_ph->addColumn('project_id',             'int');
$tb_ph->addColumn('project_hour_type_id',   'int');
$tb_ph->addColumn('project_hour_status_id', 'int');
$tb_ph->addColumn('registration_type',      'enum(\'from_to\',\'duration\')', ['default' => 'from_to']);
$tb_ph->addColumn('short_description',      'longtext');
$tb_ph->addColumn('long_description',       'longtext');
$tb_ph->addColumn('start_time',             'datetime');
$tb_ph->addColumn('end_time',               'datetime');
$tb_ph->addColumn('duration',               'double');
$tb_ph->addColumn('edited',                 'datetime');
$tb_ph->addColumn('created',                'datetime');
$tb_ph->addColumn('user_id',                'int');
$tb_ph->addColumn('declarable',             'boolean');
$tb_ph->addIndex('project_id', ['project_id']);
$tb_ph->addIndex('project_hour_type_id', ['project_hour_type_id']);
$tbs[] = $tb_ph;

return $tbs;


