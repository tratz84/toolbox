<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use core\controller\BaseReportController;
use project\form\ProjectHourReportForm;
use project\service\ProjectService;

class hoursController extends BaseReportController {

    public function report($blnRender = true) {
        $projectService = $this->oc->get(ProjectService::class);

        if (get_var('reportAction') == 'status') {
            $ids = explode(',', get_var('ids'));

            $projectService->updateHourStatus(get_var('projectHourStatusId'), $ids);
        }


        $this->form = new ProjectHourReportForm();
        $this->form->bind($_REQUEST);


        $this->lrHours = $projectService->searchHour(0, 100, $_REQUEST);


        $this->hourStatuses = $projectService->readHourStatuses();

        if ($blnRender) {
            return $this->renderToString();
        }
    }

    public function action_xls() {
        $this->report( false );

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->setActiveSheetIndex(0);//->setCellValue('A1', 'Hello')

        $this->xlsHeader($sheet, array('Gebruiker', 'Klant', 'Project', 'Omschrijving', 'Lange omschrijving', 'Start', 'Eind', 'Duur', 'Declarabel', 'Status', 'Aangemaakt op'));

        $objs = $this->lrHours->getObjects();
        for($rowno=0; $rowno < count($objs); $rowno++) {
            $c = $objs[$rowno];

            $this->xlsCol($sheet, $rowno+2, 1, $c['username']);
            if ($c['company_name']) {
                $this->xlsCol($sheet, $rowno+2, 2, $c['company_name']);
            } else {
                $this->xlsCol($sheet, $rowno+2, 2, format_personname($c));
            }

            $this->xlsCol($sheet, $rowno+2, 3, $c['project_name']);
            $this->xlsCol($sheet, $rowno+2, 4, $c['short_description']);
            $this->xlsCol($sheet, $rowno+2, 5, $c['long_description']);
            $this->xlsCol($sheet, $rowno+2, 6, $c['start_time'], 'datetime');
            $this->xlsCol($sheet, $rowno+2, 7, $c['end_time'], 'datetime');
            $this->xlsCol($sheet, $rowno+2, 8, myround($c['total_minutes']/60,2));
            $this->xlsCol($sheet, $rowno+2, 9, $c['declarable'] ? true : false);
            $this->xlsCol($sheet, $rowno+2, 10, $c['status_description']);
            $this->xlsCol($sheet, $rowno+2, 11, $c['created'], 'datetime');
        }

        $this->outputExcel($spreadsheet, 'project-hours.xlsx');
        exit;
    }

}
