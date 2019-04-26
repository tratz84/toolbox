<?php



use core\controller\BaseController;
use filesync\service\StoreService;

class archiveOverviewController extends BaseController {
    
    
    public function action_index() {
        
        $storeService = $this->oc->get(StoreService::class);
        
        
        $companyId = $this->form->getWidgetValue('company_id');
        $personId = $this->form->getWidgetValue('person_id');
        
        if ($companyId) {
            $this->storeFiles = $storeService->readArchiveFilesByCompany($companyId);
        }
        if ($personId) {
            $this->storeFiles = $storeService->readArchiveFilesByPerson($personId);
        }
        
        
        if (!$companyId && !$personId) {
            return;
        }
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
}
