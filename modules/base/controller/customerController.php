<?php


use base\service\CustomerService;
use core\controller\BaseController;
use base\service\CompanyService;
use base\service\PersonService;

class customerController extends BaseController {
    
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $customerService = $this->oc->get(CustomerService::class);
        
        $r = $customerService->search($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_select2() {

        $customerService = $this->oc->get(CustomerService::class);
        
        $r = $customerService->search(0, 20, $_REQUEST);
        
        
        $arr = array();
        
        if (isset($_REQUEST['name']) == false || trim($_REQUEST['name']) == '') {
            $arr[] = array(
                'id' => '0',
                'text' => 'Maak uw keuze'
            );
        }
        foreach($r->getObjects() as $customer) {
            $arr[] = array(
                'id' => $customer['type'] . '-' . $customer['id'], 
                'text' => $customer['name']
            );
        }
        
        
        $result = array();
        $result['results'] = $arr;
        
        $this->json($result);
        
    }
    
    
    public function action_emailaddresses() {
        
        $companyId = null;
        $personId = null;
        
        if (get_var('customer_id')) {
            if (strpos(get_var('customer_id'), 'company-') === 0) {
                $companyId = (int)substr(get_var('customer_id'), strlen('company-'));
            }
            if (strpos(get_var('customer_id'), 'person-') === 0) {
                $personId = (int)substr(get_var('customer_id'), strlen('person-'));
            }
        }
        
        if (!$companyId && get_var('company_id')) {
            $companyId = (int)get_var('company_id');
        }
        if (!$personId && get_var('person_id')) {
            $personId = (int)get_var('person_id');
        }
        
        $addresses = array();
        
        if ($companyId) {
            /** @var CompanyService $companyService */
            $companyService = object_container_get(CompanyService::class);
            
            $company = $companyService->readCompany($companyId, ['null-if-not-found' => true]);
            
            if ($company) foreach($company->getEmailList() as $e) {
                $addresses[] = array(
                    'name' => $e->getDescription(),
                    'email' => $e->getEmailAddress()
                );
            }
        }
        if ($personId) {
            /** @var PersonService $personService */
            $personService = object_container_get(PersonService::class);
            
            $person = $personService->readPerson($personId);
            
            if ($person) foreach($person->getEmailList() as $e) {
                $addresses[] = array(
                    'name' => $e->getDescription(),
                    'email' => $e->getEmailAddress()
                );
            }
        }
        
        return $this->json([
            'success' => true,
            'addresses' => $addresses
        ]);
    }
    
    
}


