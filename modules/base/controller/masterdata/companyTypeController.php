<?php



use base\forms\CompanyTypeForm;
use base\model\CompanyType;
use base\service\CompanyService;
use core\controller\BaseController;
use core\forms\lists\ListResponse;

class companyTypeController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_search() {
        $companyService = $this->oc->get(CompanyService::class);
        
        $companyTypes = $companyService->readTypes();
        
        $list = array();
        foreach($companyTypes as $ct) {
            $list[] = $ct->getFields(array('company_type_id', 'type_name', 'default_selected'));
        }
        
        
        $lr = new ListResponse(0, count($companyTypes), count($companyTypes), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $companyService = $this->oc->get(CompanyService::class);
        if ($id) {
            $companyType = $companyService->readCompanyType($id);
        } else {
            $companyType = new CompanyType();
        }
        
        
        $form = new CompanyTypeForm();
        $form->bind($companyType);
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            if ($form->validate()) {
                $companyService->saveCompanyType($form);
                
                redirect('/?m=base&c=masterdata/companyType');
            }
            
        }
        
        
        
        $this->isNew = $companyType->isNew();
        $this->form = $form;
        
        
        $this->render();
        
    }
    
    public function action_delete() {
        $companyService = $this->oc->get(CompanyService::class);
        
        $companyService->deleteCompanyType($_REQUEST['id']);

        redirect('/?m=base&c=masterdata/companyType');
    }
    
    public function action_sort() {
        $companyService = $this->oc->get(CompanyService::class);
        $companyService->updateCompanyTypeSort( $_REQUEST['ids'] );
        
        print 'OK';
    }
    
}