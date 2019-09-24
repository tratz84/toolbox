<?php


use core\controller\BaseController;
use project\service\ProjectService;



class summaryPerMonthController extends BaseController {

    public function action_index() {
        
        $start = get_var('start');
        $end = get_var('end');
        
        // set defaults
        if (!$start || preg_match('/^\\d{4}-\\d{2}$/', $start) == false) {
            $start = date('Y-m', strtotime('-1 year'));
        }
        if (!$end || preg_match('/^\\d{4}-\\d{2}$/', $end) == false) {
            $end = date('Y-m');
        }
        
        // fetch
        $projectService = object_container_get(ProjectService::class);
        $totals = $projectService->totalsPerMonth($start, $end);
        
        // response
        $this->json([
            'label' => 'Project uren',
            'data' => $totals
        ]);
    }
}

