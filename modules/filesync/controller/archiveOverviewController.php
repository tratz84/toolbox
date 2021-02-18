<?php



use core\controller\BaseController;
use filesync\service\StoreService;
use filesync\form\ArchiveCustomerIndexTable;

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
        
        $this->archiveCustomerIndexTable = new ArchiveCustomerIndexTable();
        $this->archiveCustomerIndexTable->setContainerId('#archive-customer-index-table');
        if ($companyId) {
            $this->archiveCustomerIndexTable->setCompanyId( $companyId );
        }
        if ($personId) {
            $this->archiveCustomerIndexTable->setPersonId( $personId );
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
    
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $storeService = object_container_get( StoreService::class );
        
        $r = $storeService->searchFile($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
}
