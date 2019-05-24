<?php



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use base\service\CustomerService;
use core\controller\BaseReportController;

class customerReportController extends BaseReportController {
    
    
    public function report($render=true) {
        
        
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
        
        
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        
        $this->xlsHeader($sheet, array('Type', 'Id', t('Name'), t('Coc number'), t('VAT number'), 'IBAN', 'BIC', t('Street').' 1', t('Housenr').' 1', t('Zipcode').' 1', t('City').' 1', t('Email'), t('Phonenr'), t('Last modified'), t('Created on')));
        
        $objs = $listResponse->getObjects();
        
        for($rowno=0; $rowno < count($objs); $rowno++) {
            $c = $objs[$rowno];
            
            if ($c['person_id']) {
                $this->xlsCol($sheet, $rowno+2, 1, t('Private'));
                $this->xlsCol($sheet, $rowno+2, 2, $c['person_id']);
                $this->xlsCol($sheet, $rowno+2, 3, format_personname($c));
            }
            if ($c['company_id']) {
                $this->xlsCol($sheet, $rowno+2, 1, t('Company'));
                $this->xlsCol($sheet, $rowno+2, 2, $c['company_id']);
                $this->xlsCol($sheet, $rowno+2, 3, $c['company_name']);
            }
            
            @$this->xlsCol($sheet, $rowno+2, 4, $c['coc_number']);
            @$this->xlsCol($sheet, $rowno+2, 5, $c['vat_number']);
            @$this->xlsCol($sheet, $rowno+2, 6, $c['iban']);
            @$this->xlsCol($sheet, $rowno+2, 7, $c['bic']);
            @$this->xlsCol($sheet, $rowno+2, 8, $c['street']);
            @$this->xlsCol($sheet, $rowno+2, 9, $c['street_no']);
            @$this->xlsCol($sheet, $rowno+2, 10, $c['zipcode']);
            @$this->xlsCol($sheet, $rowno+2, 11, $c['city']);
                
            @$this->xlsCol($sheet, $rowno+2, 12, $c['email_address']);
            @$this->xlsCol($sheet, $rowno+2, 13, $c['phonenr']);
            @$this->xlsCol($sheet, $rowno+2, 14, $c['edited'], 'datetime');
            @$this->xlsCol($sheet, $rowno+2, 15, $c['created'], 'datetime');
        }
        
        $this->outputExcel($spreadsheet, 'customerExport.xlsx');
    }
}
