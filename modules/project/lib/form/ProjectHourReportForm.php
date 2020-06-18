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
        $this->addWidget(new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=customer&c=customer&a=select2', 'Klant') );
        $this->addStatus();

    }



    public function bind($obj) {
        parent::bind($obj);

        $companyId = null;
        $personId = null;

        $customerWidget = $this->getWidget('customer_id');

        if (is_array($obj) && isset($obj['customer_id'])) {

            if (strpos($obj['customer_id'], 'company-') === 0) {
                $companyId = str_replace('company-', '', $obj['customer_id']);
            }
            else if (strpos($obj['customer_id'], 'person-') === 0) {
                $personId = str_replace('person-', '', $obj['customer_id']);
            }

        }

        if ($companyId) {
            $customerWidget->setValue('company-'.$companyId);

            $cs = ObjectContainer::getInstance()->get(CompanyService::class);
            $name = $cs->getCompanyName($companyId);

            $customerWidget->setDefaultText( $name );
        }
        else if ($personId) {
            $customerWidget->setValue('person-'.$personId);

            $ps = ObjectContainer::getInstance()->get(PersonService::class);
            $fullname = $ps->getFullname($personId);

            $customerWidget->setDefaultText( $fullname );
        }


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
