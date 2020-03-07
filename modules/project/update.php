<?php



use core\db\TableModel;
use core\db\mysql\MysqlTableGenerator;

$tm_project = new TableModel('project', 'project');
$tm_project->addColumn('project_id',   'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);

$tm_project->addColumn('project_hours', 'int');
$tm_project->addColumn('project_billable_type',  'enum(\'fixed\',\'ongoing\')');
$tm_project->addColumn('hourly_rate',  'decimal(10, 2)');

$tm_project->addColumn('company_id',   'int');
$tm_project->addColumn('person_id',    'int');
$tm_project->addColumn('project_name', 'varchar(255)');
$tm_project->addColumn('active',       'boolean');
$tm_project->addColumn('note',         'longtext');
$tm_project->addColumn('edited',       'datetime');
$tm_project->addColumn('created',      'datetime');
$tm_project->setIndex('person_id', array('person_id'));


$mtg_project = new MysqlTableGenerator($tm_project);
$mtg_project->executeDiff();


