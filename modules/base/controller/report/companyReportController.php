<?php



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use base\service\CompanyService;
use core\controller\BaseReportController;

class companyReportController extends BaseReportController {
    
    
    public function report($render=true) {
        
        $companyService = $this->oc->get(CompanyService::class);
        $this->companies = $companyService->readReport();
        
        if ($render)
            return $this->renderToString();
    }
    
    
    public function action_xls() {
        $this->report(false);
        
        
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->setActiveSheetIndex(0);//->setCellValue('A1', 'Hello')
        
        $this->xlsHeader($sheet, array('Id', 'Bedrijfsnaam', 'Kvknr', 'Btw nr', 'Iban', 'Bic', 'Notitie', 'Laatst bewerkt', 'Aangemaakt op'));
        
        for($rowno=0; $rowno < count($this->companies); $rowno++) {
            $c = $this->companies[$rowno];
            
            $this->xlsCol($sheet, $rowno+2, 1, $c->getCompanyId());
            $this->xlsCol($sheet, $rowno+2, 2, $c->getCompanyName());
            $this->xlsCol($sheet, $rowno+2, 3, $c->getCocNumber());
            $this->xlsCol($sheet, $rowno+2, 4, $c->getVatNumber());
            $this->xlsCol($sheet, $rowno+2, 5, $c->getIban());
            $this->xlsCol($sheet, $rowno+2, 6, $c->getBic());
            $this->xlsCol($sheet, $rowno+2, 7, $c->getNote());
            $this->xlsCol($sheet, $rowno+2, 8, $c->getEdited(), 'datetime');
            $this->xlsCol($sheet, $rowno+2, 9, $c->getCreated(), 'datetime');
        }
        
        $this->outputExcel($spreadsheet, 'companiesExport.xlsx');
    }
}
