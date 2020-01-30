<?php


use core\controller\BaseController;
use invoice\service\InvoiceService;

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
        $invoiceService = object_container_get(InvoiceService::class);
        $totals = $invoiceService->totalsPerMonth($start, $end);
        
        // response
        $this->json([
            'label' => 'Factuur bedragen',
            'data' => $totals
        ]);
    }
    
}
