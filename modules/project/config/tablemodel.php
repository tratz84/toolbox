<?php


use core\db\TableModel;

$tbs = array();


$tb_project = new TableModel('project', 'project');
$tb_project->addColumn('project_id',             'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_project->addColumn('project_hours',          'int');
$tb_project->addColumn('project_billable_type',  'enum(\'fixed\',\'ongoing\')');
$tb_project->addColumn('hourly_rate',            'decimal(10, 2)');
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


return $tbs;


