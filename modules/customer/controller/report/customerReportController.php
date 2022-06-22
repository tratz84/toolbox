<?php



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use customer\service\CustomerService;
use core\controller\BaseReportController;
use customer\form\CustomerReportIndexTable;

class customerReportController extends BaseReportController {
    
    
    public function report($render=true) {
        
        $this->it = object_container_create( CustomerReportIndexTable::class );
        
        if ($render)
            return $this->renderToString();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = 500;
        
        
        $customerService = $this->oc->get(CustomerService::class);
        
        $listResponse = $customerService->readReport($pageNo*$limit, $limit);
        
        $arr = array();
        $arr['listResponse'] = $listResponse;
        
        $this->json($arr);
    }
    
    
    public function action_xls() {
        $this->report(false);
        
        $customerService = $this->oc->get(CustomerService::class);
        $listResponse = $customerService->readReport(0, 99999);
        
        $objects = $listResponse->getObjects();
        for($x=0; $x < count($objects); $x++) {
            if ($objects[$x]['person_id']) {
                $objects[$x]['customer_id'] = $objects[$x]['person_id'];
                $objects[$x]['type'] = t('Private');
            }
            else {
                $objects[$x]['customer_id'] = $objects[$x]['company_id'];
                $objects[$x]['type'] = t('Company');
            }
            $objects[$x]['name'] = format_customername( $objects[$x] );
        }
        $listResponse->setObjects( $objects );
        
        
        $it = object_container_create( CustomerReportIndexTable::class );
        $lree = $it->createListResponseExcelExport();
        
        @$lree->export($listResponse, 'customerExport.xlsx');
    }
}
