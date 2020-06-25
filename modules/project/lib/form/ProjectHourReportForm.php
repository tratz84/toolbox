<?php

namespace project\form;


use customer\service\CompanyService;
use customer\service\PersonService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\DynamicSelectField;
use core\forms\HiddenField;
use core\forms\SelectField;
use customer\forms\CustomerSelectWidget;
use invoice\model\Invoice;

use project\service\ProjectService;

class ProjectHourReportForm extends BaseForm {



    public function __construct() {
        parent::__construct();

        // $this->addWidget(new HiddenField('m', 'report'));
        // $this->addWidget(new HiddenField('c', 'report'));
        // $this->addWidget(new HiddenField('controllerName', 'project@report/hours'));

        $this->addWidget(new DatePickerField('start', '', 'Startdatum'));
        $this->addWidget(new DatePickerField('end', '', 'Einddatum'));
        $this->addWidget(new CustomerSelectWidget() );
        $this->addStatus();

    }


    protected function addStatus() {
        $projectService = ObjectContainer::getInstance()->get(ProjectService::class);
        $status = $projectService->readHourStatuses();

        $mapStatus = array();
        $mapStatus[''] = 'Maak uw keuze';
        foreach($status as $s) {
            $mapStatus[$s->getProjectHourStatusId()] = $s->getDescription();
        }

        $this->addWidget(new SelectField('project_hour_status_id', '', $mapStatus, 'Status'));
    }

}
