<?php



use core\controller\BaseController;
use filesync\service\StoreService;

class archiveOverviewController extends BaseController {
    
    public function init() {
        checkCapability('filesync', 'manager');
    }
    
    
    public function action_index() {
        
        $storeService = $this->oc->get(StoreService::class);
        
        
        $companyId = $this->form->getWidgetValue('company_id');
        $personId = $this->form->getWidgetValue('person_id');
        
        if ($companyId) {
            $this->storeFiles = $storeService->readFilesByCompany($companyId);
        }
        if ($personId) {
            $this->storeFiles = $storeService->readFilesByPerson($personId);
        }
        
        
        $this->filetemplates = array();
        if (isset($this->template_ids) && is_array($this->template_ids)) foreach( $this->template_ids as $tid) {
            $this->filetemplates[] = filesync_get_filetemplate( $tid );
        }
        
        
        if (!$companyId && !$personId) {
            return;
        }
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
}
