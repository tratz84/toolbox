<?php


use base\externalapi\VatCheckApiService;
use base\forms\CompanyForm;
use base\model\Address;
use base\model\Company;
use base\model\Email;
use base\model\Phone;
use base\service\CompanyService;
use core\Context;
use core\container\ActionContainer;
use core\controller\BaseController;
use core\event\ActionValidationEvent;
use core\event\EventBus;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;

class companyController extends BaseController {

    public function init() {
        if (Context::getInstance()->isCompaniesEnabled() == false)
            throw new InvalidStateException('Company-module not activated');
        
        $this->addTitle(t('Companies'));
    }
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
       
        $companyService = $this->oc->get(CompanyService::class);
        
        $r = $companyService->search($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['company_id'])?(int)$_REQUEST['company_id']:0;
        
        $companyService = $this->oc->get(CompanyService::class);
        if ($id) {
            try {
                $company = $companyService->readCompany($id);
                
                if ($company->getDeleted()) {
                    throw new ObjectNotFoundException('Company marked as deleted');
                }
                
                $this->addTitle($company->getCompanyName());
            } catch (ObjectNotFoundException $ex) {
                return $this->renderError( $ex->getMessage() );
            }
        } else {
            $this->addTitle(t('New company'));
            
            $company = new Company();
        }
        
        $companyForm = object_container_create(CompanyForm::class);
        $companyForm->bind($company);

        if (is_post()) {
            $companyForm->bind($_REQUEST);
            
            if ($companyForm->validate()) {
                $companyId = $companyService->save($companyForm);
                
                report_user_message(t('Changes saved'));
                redirect('/?m=base&c=company&a=edit&company_id='.$companyId);
            }
            
        }
        
        
        
        $this->isNew = $company->isNew();
        $this->form = $companyForm;
        
        $this->actionContainer = new ActionContainer('company', $company->getCompanyId());
        hook_eventbus_publish($this->actionContainer, 'company', 'company-edit');
        
        $this->render();
    }
    
    
    public function action_delete() {
        $id = isset($_REQUEST['company_id'])?(int)$_REQUEST['company_id']:0;
        
        $companyService = $this->oc->get(CompanyService::class);
        $company = $companyService->readCompany($id);
        
        /**
         * @var EventBus $eventBus
         */
        $eventBus = $this->oc->get(EventBus::class);
        $evt = $eventBus->publish(new ActionValidationEvent($company, 'base', 'company-delete'));
        
        if ($evt->hasErrors()) {
            report_user_error($evt->getErrors());
        } else {
            $companyService->delete($id);
        }
        
        redirect('/?m=base&c=company');
    }
    
    
    public function action_widget() {
        
        if (isset($this->companyForm)) {
            $this->company = new Company();
            $this->companyForm->fill($this->company, array('company_type_id', 'company_name', 'contact_person', 'coc_number', 'vat_number', 'note', 'iban', 'bic'));
            $this->company->setAddressList( $this->companyForm->getWidget('addressList')->asObjects( Address::class) );
            $this->company->setEmailList( $this->companyForm->getWidget('emailList')->asObjects( Email::class) );
            $this->company->setPhoneList( $this->companyForm->getWidget('phoneList')->asObjects( Phone::class) );
            
        } else {
            $companyService = $this->oc->get(CompanyService::class);
            $this->company = $companyService->readCompany($this->company_id);
        }
        
        $this->render();
    }
    
    
    public function action_view_vat_number() {
        $vcaService = object_container_get(VatCheckApiService::class);
        
        $this->nr = get_var('nr');
        
        try {
            $this->validVat = $vcaService->validateVat( $this->nr );
            $this->vatInfo = $vcaService->vatInfo( $this->nr );
//             var_export($this->response);exit;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
        }
        
        $this->render();
    }
    
    public function action_check_vat_number() {
        $vcaService = object_container_get(VatCheckApiService::class);
        
        $r = array();
        $r['success'] = false;
        
        try {
            $validVat = $vcaService->validateVat( get_var('vat_number') );
            if ($validVat) {
                $r['success'] = true;
                $r['data'] = (array)$vcaService->vatInfo( get_var('vat_number') );;
            }
        } catch (\Exception $ex) {
            $r['error'] = $ex->getMessage();
        }
        
        $this->json( $r );
    }
    
    
    
    public function action_popup() {
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
}


