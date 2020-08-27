<?php



use core\controller\BaseController;
use filesync\service\StoreService;

class archiveOverviewController extends BaseController {
    
    public function init() {
        checkCapability('filesync', 'manager');
    }
    
    
    public function action_index() {
        
        $storeService = $this->oc->get(StoreService::class);
        
        if (isset($this->form)) {
            $companyId = $this->form->getWidgetValue('company_id');
            $personId = $this->form->getWidgetValue('person_id');
        } else {
            $companyId = get_var('companyId');
            $personId = get_var('personId');
            $this->template_ids = get_var('template_ids');
        }
        
        if ($companyId) {
            $this->storeFiles = $storeService->readFilesByCompany($companyId);
        }
        if ($personId) {
            $this->storeFiles = $storeService->readFilesByPerson($personId);
        }
        
        
        $this->filetemplates = array();
        if (isset($this->template_ids) && is_array($this->template_ids)) foreach( $this->template_ids as $tid) {
            $ft = filesync_get_filetemplate( $tid );
            
            if ($ft->getStoreFileId()) {
                $this->filetemplates[] = $ft;
            }
        }
        
        
        if (!$companyId && !$personId) {
            return;
        }
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
}
